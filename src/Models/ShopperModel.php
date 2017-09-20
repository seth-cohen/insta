<?php

namespace IC\Models;

class ShopperModel extends BaseModel implements \JsonSerializable {

  protected $firstName = '';

  protected $lastName = '';

  protected $id;

  protected $emailAddress = '';

  protected $phone = '';

  protected $zipCode = '';

  protected $workflowState = '';

  protected $errors = [];

  /**
   * Factory method to create an "in memory" shopper model
   *
   * @param string $firstName    First name of the Shopper
   * @param string $lastName     Last name of the Shopper
   * @param string $emailAddress Emailaddress of the Shopper
   * @param string $phoneNumber
   * @param string $zipCode
   * @param string $workflowState
   *
   * @return \IC\Models\ShopperModel
   */
  public static function create(
      string $firstName,
      string $lastName,
      string $emailAddress,
      string $phoneNumber,
      string $zipCode = null,
      string $workflowState = null
  ) {
    $model                = new static();
    $model->firstName     = $firstName;
    $model->lastName      = $lastName;
    $model->emailAddress  = $emailAddress;
    $model->phone         = $phoneNumber;
    $model->zipCode       = $zipCode;
    $model->workflowState = $workflowState;

    return $model;
  }

  public function getId() {
    return (int)$this->id;
  }

  /**
   * @return string
   */
  public function getFullName() {
    return $this->firstName . ' ' . $this->lastName;
  }

  /**
   * @return string
   */
  public function getFirstName() {
    return $this->firstName;
  }

  /**
   * @param string $name
   *
   * @return $this
   */
  public function setFirstName(string $name) {
    $this->firstName = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getLastName() {
    return $this->lastName;
  }

  /**
   * @param string $name
   *
   * @return $this
   */
  public function setLastName(string $name) {
    $this->lastName = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getEmailAddress() {
    return $this->emailAddress;
  }

  /**
   * Set the email address
   *
   * @param string $email
   *
   * @return $this
   */
  public function setEmailAddress(string $email) {
    $this->emailAddress = $email;

    return $this;
  }

  /**
   * @param string $phone
   *
   * @return $this
   */
  public function setPhone(string $phone) {
    $this->lastName = $phone;

    return $this;
  }

  /**
   * @return string
   */
  public function getPhone() {
    return $this->phone;
  }

  /**
   * @param string $zipCode
   *
   * @return $this
   */
  public function setZipCode(string $zipCode) {
    $this->lastName = $zipCode;

    return $this;
  }

  /**
   * @return string
   */
  public function getZipCode() {
    return $this->zipCode;
  }

  /**
   * @param string $workflowState
   *
   * @return $this
   */
  public function setWorkflowState(string $workflowState) {
    $this->workflowState = $workflowState;

    return $this;
  }

  /**
   * @return string
   */
  public function getWorkflowState() {
    return $this->workflowState;
  }

  /**
   * @return array
   */
  public function getErrors() {
    return $this->errors;
  }

  public function validate() {
    $this->errors = [];
    $isValid      = parent::validate();

    if (!\IC\Helpers\ValidationHelper::isValidEmail($this->emailAddress)) {
      $this->errors[] = 'Invalid Email Address';
      $isValid        = false;
    }

    if (!\IC\Helpers\ValidationHelper::isValidPhone($this->phone)) {
      $this->errors[] = 'Invalid Phone';
      $isValid        = false;
    }

    //@todo More validation
    return $isValid;
  }

  /**
   * Specify data which should be serialized to JSON
   *
   * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
   * @return mixed data which can be serialized by <b>json_encode</b>,
   *        which is a value of any type other than a resource.
   */
  function jsonSerialize() {
    return [
        'firstName'     => $this->firstName,
        'lastName'      => $this->lastName,
        'emailAddress'  => $this->emailAddress,
        'phone'         => $this->phone,
        'zipCode'       => $this->zipCode,
        'workflowState' => $this->workflowState
    ];
  }
}
