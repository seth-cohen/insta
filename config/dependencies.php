<?php
/**
 * Configuration of the Dependency Injection Container
 */
$container = $app->getContainer();

$container['view'] = function(\Slim\Container $c) {
  $settings = $c->get('settings')['view'] ?? [];

  $view = new \Slim\Views\Twig(
      $settings['templates_path'] //,
      /*['cache' => $settings['cache_path']]*/
  );

  // Instantiate and add Slim specific extension
  $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
  $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));

  return $view;
};

$container['logger'] = function(\Slim\Container $c) {
  $settings = $c->get('settings')['logger'] ?? [];
  $logger = new \Monolog\Logger($settings['name'] ?? 'default');
  $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
  $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'] ?? 'app.log', $settings['level'] ?? Monolog\Logger::DEBUG));

  return $logger;
};

$container['pdo'] = function(\Slim\Container $c) {
  try {
    $dbSettings = $c->get('settings')['db'] ?? [];
    $path = $dbSettings['path'] ?? '';

    $pdo = new \PDO('sqlite:' . $path);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

    return $pdo;
  } catch (\PDOException $e) {
    throw new \ErrorException($e->getMessage());
  }
};

// Registering controllers with the container. The reason for this, is somewhat of a limitation of Slim. This allows
// us to control (no pun intended) and document the dependencies required by our controller otherwise the framework will
// inject the entire container (which would be essentially a super global
$container[\IC\Controllers\ShopperOnboardingController::class] = function ($c) {
  $pdo = $c->get('pdo');
  $shopperDAO = new \IC\DAOs\ShopperSQLDAO($pdo);
  $shopperService = new \IC\Services\ShopperService($shopperDAO);

  $view = $c->get('view');
  $logger = $c->get('logger');

  return new \IC\Controllers\ShopperOnboardingController($view, $shopperService, $logger);
};
