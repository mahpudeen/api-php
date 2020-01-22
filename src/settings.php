<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'otten32run-web',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database Settings
        'db' => [
            'host' => '104.154.86.126',
            'user' => 'otten32run',
            'pass' => 'qwerty123',
            'dbname' => 'db_otten32run',
            'driver' => 'mysql'
        ]
    ],
];
