<?php
return [
    'database' => [
        'name' => 'grabber',
        'username' => 'root',
        'password' => 'lampolampo',
        'connection' => 'mysql:host=127.0.0.1',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ]
];
