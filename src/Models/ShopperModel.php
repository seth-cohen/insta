<?php

namespace IC\Models;

class ShopperModel extends BaseModel implements \JsonSerializable {

  protected $firstName = '';

  protected $lastName = '';

  protected $id;

  protected $emailAddress = '';

  /**
   * Factory method to create an "in memory" shopper model
   *
   * @param string $firstName    First name of the Shopper
   * @param string $lastName     Last name of the Shopper
   * @param string $emailAddress Emailaddress of the Shopper
   *
   * @return \IC\Models\ShopperModel
   */
  public static function create(
      string $firstName,
      string $lastName,
      string $emailAddress
  ) {
    $model = new static();
    $model->firstName = $firstName;
    $model->lastName = $lastName;
    $model->emailAddress = $emailAddress;

    return $model;
  }

  public function getId() {
    return $this->id;
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
   * @param string $email
   */
  public function setEmailAddress(string $email) {

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
        'firstName' => $this->firstName,
        'lastName' => $this->lastName,
        'emailAddress' => $this->emailAddress
    ];
  }
}
