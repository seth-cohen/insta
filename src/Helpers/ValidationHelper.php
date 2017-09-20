<?php

namespace IC\Helpers;

class ValidationHelper {
  public static function isValidEmail(string $emailAddress) {
    return filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
  }

  public static function isValidPhone(string $phoneNumber) {
    $regex = '/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i';

    return (preg_match($regex, $phoneNumber));
  }

  public static function isValidZip(string $zipCode) {
    return preg_match('/^([0-9]{5})(-[0-9]{4})?$/i',$zipCode);
  }
}
