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

    // would actually only do this if they were logged in, otherwise PII compliance issues :D
    if (!empty($email)) {
      $shopper = $this->shopperService->getShopperByEmail($email);
    }

    return $this->view->render(
        $response,
        'shopper_landing.html',
        [
            'shopper' => !empty($shopper) ? json_decode(json_encode($shopper), true) : $args['shopper'] ?? [],
            'errors'  => $args['errors'] ?? []
        ]
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
    // Ensure that we have all of the required fields
    $firstName    = trim($request->getParsedBodyParam('first_name', ''));
    $lastName     = trim($request->getParsedBodyParam('last_name', ''));
    $emailAddress = trim($request->getParsedBodyParam('email', ''));
    $phone        = trim($request->getParsedBodyParam('phone', ''));
    $zipCode      = trim($request->getParsedBodyParam('zip', ''));

    $errors = $this->validateInputs($firstName, $lastName, $emailAddress, $phone, $zipCode);

    if (!empty($errors)) {
      $this->logger->debug('Errors with input', $errors);

      return $this->landing(
          $request,
          $response,
          [
              'errors'  => $errors,
              'shopper' => [
                  'firstName'    => $firstName,
                  'lastName'     => $lastName,
                  'emailAddress' => $emailAddress,
                  'phone'        => $phone,
                  'zipCode'      => $zipCode
              ]
          ]
      );
    }

    // If we already have a shopper registered with that address we should not proceed
    $shopper = $this->shopperService->getShopperByEmail($emailAddress);
    if (!empty($shopper)) {
      return $this->landing(
          $request,
          $response,
          ['errors' => ['We are already processing an application for your email account. Hold tight, we don\'t take that long']]
      );
    }

    $session = new \SlimSession\Helper();
    $session->set('shopper_email', $emailAddress);

    // Alrighty, it was tough to get here, but we made it... let's save the shopper
    $shopperData = [
        'firstName'     => $firstName,
        'lastName'      => $lastName,
        'emailAddress'  => $emailAddress,
        'phone'         => $phone,
        'zipCode'       => $zipCode,
        'workflowState' => 'applied'
    ];

    $this->logger->debug('Saving shopper with details: ', $shopperData);
    $model = $this->shopperService->create($shopperData);

    if (!$model->getId()) {
      $this->logger->debug('There was an error saving shopper', $model->getErrors());
    } else {
      $this->logger->debug(
          'Successfully saved user: ' . $model->getEmailAddress(),
          json_decode(json_encode($model), true)
      );
    }

    return $response->withRedirect('/shopper/background');
  }

  /**
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param array                                    $args
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function background_check(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {
    return $this->view->render(
        $response,
        'background_check.html',
        ['errors' => $args['errors'] ?? []]
    );
  }

  /**
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param array                                    $args
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function background_check_submit(
      ServerRequestInterface $request,
      ResponseInterface $response,
      array $args = []
  ) {
    // Get customer from session and update their application workflow status
    $session = new \SlimSession\Helper();
    $email   = $session->get('shopper_email', '');

    // would actually only do this if they were logged in, otherwise PII compliance issues :D
    if (!empty($email)) {
      $shopper = $this->shopperService->getShopperByEmail($email);
    }

    if (empty($shopper)) {
      $this->logger->debug('Couldn\'t find shopper to update', ['emailAddress' => $email]);

      return $this->background_check($request, $response, ['errors' => ['Error processing request please try again']]);
    }

    $shopper->setWorkflowState('backgound_authorized');
    $this->shopperService->updateShopper($shopper);

    return $response->withRedirect('/shopper/confirmed');
  }

  /**
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param array                                    $args
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function confirmation(ServerRequestInterface $request, ResponseInterface $response, array $args = []) {
    return $this->view->render(
        $response,
        'confirmed.html'
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

    if (empty($emailAddress) || !\IC\Helpers\ValidationHelper::isValidEmail($emailAddress)) {
      $errors['emailAddress'] = 'Email Address is needed to complete sign up';
    }

    if (empty($phone) || !\IC\Helpers\ValidationHelper::isValidPhone($phone)) {
      $errors['phone'] =
          'Phone number is required. We promise not to call often, only when we have something cool to discuss';
    }

    if (empty($zipCode) || !\IC\Helpers\ValidationHelper::isValidZip($zipCode)) {
      $errors['zip'] = 'A valid zipcode is required so we know where you\'ll be shopping';
    }

    return $errors;
  }
}
