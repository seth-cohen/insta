<?php

$app->get(
    '/shopper/landing',
    function($request, $response) {
      return $this->view->render(
          $response,
          'shopper_landing.html',
          ['name' => 'InstaCart']
      );
    }
)->setName('shopper-landing');
