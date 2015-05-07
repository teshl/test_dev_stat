<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once 'config_db.php';
require_once 'service\Format.php';

$STATUS = [
    1 => 'новый',
    2 => 'зарегистрирован',
    3 => 'отказался',
    4 => 'недоступен'
];

// проверка на ajax
if(    isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    && isset($_POST['action']) ) {

    $pdo = new PDO($db['dsn'], $db['user'], $db['password'], $db['opt']);

    $result = [];

    switch ($_POST['action']) {
        // добавить пользователя
        case 'add' :
            $values = [
                'fio' => $_POST['fio'],
                'phone' => $_POST['phone'],
                'status' => $_POST['status'],
                'date_create' => Format::DateFromTo($_POST['date_create'], 'Y-m-d H:i:s', 'd.m.Y' )
            ];

            $sql = '
              INSERT INTO `customer` ( `fio`, `phone`, `status`, `date_create`)
              VALUES ( :fio, :phone, :status, :date_create )';

            $statement = $pdo->prepare($sql);
            $result['execute'] = $statement->execute($values);

            break;

        case 'get_all' :
            $sql = 'SELECT * FROM `customer`';
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_UNIQUE);
            $result['rows'] = $rows;
            break;

        case 'edit_status' :
            $values = [
                'cid' => $_POST['cid'],
                'status' => $_POST['status']
            ];

            $sql = 'UPDATE `customer` SET `status` = :status  WHERE id = :cid';

            $statement = $pdo->prepare($sql);
            $result['execute'] = $statement->execute($values);

            break;

        case 'report':
            $values = [
                'number_days' => $_POST['number_days'],
            ];

            $sql = '
                Select
                    t_all.period as period,
                    CONVERT(IFNULL( 100*t_reg.cnt/t_all.cnt, 0),UNSIGNED)  as conversion
                FROM
                    (
                    SELECT count(*) as cnt,
                          DATEDIFF(`date_create`, t.min_date) DIV :number_days as period
                        FROM
                          `customer` c,
                          (SELECT min(`date_create`) as min_date FROM `customer`) t
                        GROUP BY period
                    ) t_all
                LEFT JOIN
                    (
                        SELECT count(*) as cnt,
                          DATEDIFF(`date_create`, t.min_date) DIV :number_days as period
                        FROM
                          `customer` c,
                          (SELECT min(`date_create`) as min_date FROM `customer`) t
                          WHERE c.status=2
                        GROUP BY period
                    ) t_reg
                ON t_all.period = t_reg.period';

            $statement = $pdo->prepare($sql);
            $result['execute'] = $statement->execute($values);
            $rows = $statement -> fetchAll(PDO::FETCH_UNIQUE);
            $result['number_days'] = $values['number_days'];

            // получаем номер последнего периода
            end($rows);
            $end_key = key($rows);

            $result['rows'] = $rows;
            $result['end_key'] = $end_key;

            break;
    }

    header('Content-Type: application/json');
    echo json_encode( $result );

}



