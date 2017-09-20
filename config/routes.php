<?php

$app->get(
    '/shopper/landing',
    function($request, $response) {
      $response->getBody()->write('Hello, World');

      return $response;
    }
)->setName('shopper-landing');
