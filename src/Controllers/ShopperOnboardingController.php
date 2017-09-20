<?php

namespace IC\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ShopperOnboardingController {

  /**
   * The Shopper Service perform required operations are delegated through this class
   *
   * @var \IC\Services\ShopperService
   */
  protected $shopperService;

  /**
   * Logging Interface
   *
   * @var \Monolog\Logger
   */
  protected $logger;

  /**
   * @var \Slim\Views\Twig
   */
  protected $view;

  /**
   * OnboardingController constructor.
   *
   * @todo Create Logger and ShopperService Interfaces
   *
   * @param \Slim\Views\Twig            $view
   * @param \IC\Services\ShopperService $shopperService
   * @param \Monolog\Logger             $logger
   */
  public function __construct(
      \Slim\Views\Twig $view,
      \IC\Services\ShopperService $shopperService,
      \Monolog\Logger $logger
  ) {
    $this->view           = $view;
    $this->shopperService = $shopperService;
    $this->logger         = $logger;
  }

  /**
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param array                                    $args
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function landing(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {

    // See if we have a shopper in the session, if so let's pre-populate their data.
    $session = new \SlimSession\Helper();
    $email = $session->get('shopper_email', '');

    if (!empty($email)) {
      $shopper = $this->shopperService->getShopperByEmail($email);
    }

    $session->set('shopper_email', 'karl.gleason@example.com');

    return $this->view->render(
        $response,
        'shopper_landing.html',
        ['shopper' => !empty($shopper) ? json_decode(json_encode($shopper), true) : []]
    );
  }

  /**
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param array                                    $args
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function landing_submit(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {
    $firstName = $request->getAttribute('name');

    $model = $this->shopperService->create(
        [
            'firstName'    => $firstName,
            'lastName'     => 'Cohen',
            'emailAddress' => 'rhico24@hotmail.com'
        ]
    );

    if (!$model->getId()) {
      $this->logger->debug('Failed to create user', ['name' => $model->getFullName()]);
// @TODO get model errors
// return error response;
    }

    return $this->view->render(
        $response,
        'shopper_landing.html',
        ['name' => $model->getFullName()]
    );
  }

}
