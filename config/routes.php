<?php

// View Landing
$app->get(
    '/shopper/landing',
    \IC\Controllers\ShopperOnboardingController::class . ':landing'
)->setName('shopper-landing');

// Form submit
$app->post(
    '/shopper/landing',
    \IC\Controllers\ShopperOnboardingController::class . ':landing_submit'
)->setName('shopper-landing-submit');

$app->get(
    '/shopper/background',
    \IC\Controllers\ShopperOnboardingController::class . ':background_check'
)->setName('background-check');

$app->post(
    '/shopper/background/submit',
    \IC\Controllers\ShopperOnboardingController::class . ':background_check_submit'
)->setName('background-check-submit');

$app->get(
    '/shopper/confirmed',
    \IC\Controllers\ShopperOnboardingController::class . ':confirmation'
)->setName('application-confirmed');
