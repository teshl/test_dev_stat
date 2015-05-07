<?php
ini_set("display_errors",1);
error_reporting(E_ALL);

require_once 'config_db.php';
require_once 'service\Format.php';

// проверка на ajax
if(    isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    && isset($_POST['action']) ) {

    $pdo = new PDO($db['dsn'], $db['user'], $db['password'], $db['opt']);

    $result = [];

    switch ($_POST['action']) {
        // добавить пользователя
        case 'get' :
            $values = [
                'number_days' => $_POST['number_days'],
            ];

            $sql = 'SELECT * FROM `customer`';
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_UNIQUE);

            break;
    }

    header('Content-Type: application/json');
    echo json_encode( $result );
}




