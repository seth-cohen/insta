<?php

// View Landing
$app->get(
    '/shopper/landing',
    \IC\Controllers\OnboardingController::class . ':landing'
)->setName('shopper-landing');

// Form submit
$app->post(
    '/shopper/landing/submit',
    \IC\Controllers\OnboardingController::class . ':landing'
)->setName('shopper-landing-submit');
