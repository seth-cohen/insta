<?php

$date = new \DateTime();

return [
    'settings' => [
        // @TODO set this to false before deploying
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'instacart-shopper',
            'level' => Monolog\Logger::DEBUG,
            // Generate new log file for each day
            'path' => __DIR__ . '/../logs/' . $date->format('Y-m-d') . 'app.log'
        ],
        // We have just the simple sqlite3 DB but would typically use something more
        // robust like MySQL or SQL Server where we can have user management and access control
        'db' => [
            'path' => __DIR__ . '/../db/applicants.sqlite3'
        ],
        'view' => [
            'templates_path' => __DIR__ . '/../src/Views/',
            'cache_path' => __DIR__ . '/../cache/twig'
        ]
    ]
];
