<?php
declare(strict_types=1);

// interop config example
return [
    // vendor name
    'doctrine' => [
        // package name
        'connection' => [
            // container id
            'orm_default' => [
                // mandatory params
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'      => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ],
        ],
        // package name
        'universal' => [
            // container id
            'orm_default' => [
                // mandatory params
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'      => [
                    'host'     => 'localhost',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ],
        ],
    ],
    'one' => [
        'two' => [
            'three' => [
                'four' => [
                    'name'  => 'test',
                    'class' => 'stdClass',
                ],
            ],
        ],
    ],
];