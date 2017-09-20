<?php

// View Landing
$app->get(
    '/shopper/landing',
    \IC\Controllers\ShopperOnboardingController::class . ':landing'
)->setName('shopper-landing');

// Form submit
$app->post(
    '/shopper/landing/submit',
    \IC\Controllers\ShopperOnboardingController::class . ':landing_submit'
)->setName('shopper-landing-submit');
