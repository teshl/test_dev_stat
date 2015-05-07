<?php

$db = [
    'host' => 'localhost',
    'dbname' => 'dz_stat',
    'user' => 'root',
    'password' => '123',
    'opt' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
];

$db['dsn'] = 'mysql:host='.$db['host'].';dbname='.$db['dbname'];