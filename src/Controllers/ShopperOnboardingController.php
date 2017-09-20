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
    $email   = $session->get('shopper_email', '');

    if (!empty($email)) {
      $shopper = $this->shopperService->getShopperByEmail($email);
    }

    return $this->view->render(
        $response,
        'shopper_landing.html',
        [
            'shopper' => !empty($shopper) ? json_decode(json_encode($shopper), true) : [],
            'errors'  => $args['errors'] ?? []
        ]
    );
  }

  public function background_check(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {

  }

  public function confirmation(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {

  }

  /**
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param array                                    $args
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function landing_submit(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {
    // Ensure that we have all of the required fields
    $firstName    = trim($request->getAttribute('first_name'));
    $lastName     = trim($request->getAttribute('last_name'));
    $emailAddress = trim($request->getAttribute('email'));
    $phone        = trim($request->getAttribute('phone_number'));
    $zipCode      = trim($request->getAttribute('zip'));

    $errors = $this->validateInputs($firstName, $lastName, $emailAddress, $phone, $zipCode);

    if (!empty($errors)) {
      var_dump('errors we have');

      return $this->landing($request, $response, ['errors' => $errors]);
    }

    // If we already have a shopper registered with that address we should not proceed
    $model = $this->shopperService->create(
        [
            'firstName'    => $firstName,
            'lastName'     => 'Cohen',
            'emailAddress' => 'rhico24@hotmail.com'
        ]
    );

    return $this->view->render(
        $response,
        'shopper_landing.html',
        ['name' => $model->getFullName()]
    );
  }

  protected function validateInputs(
      string $firstName,
      string $lastName,
      string $emailAddress,
      string $phone,
      string $zipCode
  ) {
    $errors = [];
    if (empty($firstName)) {
      $errors['firstName'] = 'Please let us know your first name so we don\'t have to be so formal.';
    }

    if (empty($lastName)) {
      $errors['lastName'] = 'We are going to need your last name to send you those checks.';
    }

    if (empty($emailAddress) || !\IC\Helpers\InputHelper::isValidEmail($emailAddress)) {
      $errors['emailAddress'] = 'Email Address is needed to complete sign up';
    }

    if (empty($phone) || !\IC\Helpers\InputHelper::isValidPhone($phone)) {
      $errors['phone'] =
          'Phone number is required. We promise not to call often, only when we have something cool to discuss';
    }

    if (empty($zipCode) || !\IC\Helpers\InputHelper::isValidZip($zipCode)) {
      $errors['zip'] = 'Zipcode is required so we know where you\'ll be shopping';
    }

    return $errors;
  }
}
