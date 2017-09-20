<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = require_once __DIR__ . '/../config/app_settings.php';
$app      = new \Slim\App($settings);
$app->add(
    new \Slim\Middleware\Session(
        [
            'name'        => 'insta_session',
            'autorefresh' => true,
            'lifetime'    => '1 hour'
        ]
    )
);

// Register our dependencies with the container
require_once __DIR__ . '/../config/dependencies.php';

// Register our routes with the App
require_once __DIR__ . '/../config/routes.php';

$app->run();
