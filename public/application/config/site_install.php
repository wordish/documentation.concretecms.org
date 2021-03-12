<?php

return [
    'canonical-url' => '',
    'canonical-url-alternative' => '',
    'session-handler' => '',
    'database' => [
        'default-connection' => 'concrete',
        'connections' => [
            'concrete' => [
                'driver' => 'c5_pdo_mysql',
                'server' => '127.0.0.1',
                'database' => 'concretecms_documentation',
                'username' => 'root',
                'password' => 'root',
                'character_set' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ],
    ],
];
