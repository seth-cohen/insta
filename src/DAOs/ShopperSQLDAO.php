<?php

namespace IC\DAOs;

class ShopperSQLDAO implements ShopperDAOInterface {

  /**
   * The interface into the database
   *
   * @var \PDO
   */
  protected $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
  }

  /**
   * Persist the Shopper to the database
   *
   * @param \IC\Models\ShopperModel $shopper
   *
   * @return int
   */
  public function save(\IC\Models\ShopperModel $shopper): int {
    $sql = '
      INSERT INTO applicants 
      (first_name, last_name, email, phone, workflow_state, created_at, updated_at)
      VALUES (:firstName, :lastName, :emailAddress, :phone, :workflow, DATETIME(), DATETIME());
    ';

    $statement = $this->pdo->prepare($sql);

    if (empty($statement)) {
      return 0;
    }

    $statement->bindValue(':firstName', $shopper->getFirstName(), \PDO::PARAM_STR);
    $statement->bindValue(':lastName', $shopper->getLastName(), \PDO::PARAM_STR);
    $statement->bindValue(':emailAddress', $shopper->getEmailAddress(), \PDO::PARAM_STR);
    $statement->bindValue(':phone', $shopper->getPhone(), \PDO::PARAM_STR);
    $statement->bindValue(':workflow', $shopper->getWorkflowState(), \PDO::PARAM_STR);

    return $statement->execute() ? (int)$this->pdo->lastInsertId() : 0;
  }

  /**
   * Persist the Shopper to the database
   *
   * @param \IC\Models\ShopperModel $shopper
   *
   * @return bool
   */
  public function update(\IC\Models\ShopperModel $shopper): int {
    $sql = '
      UPDATE applicants SET
      first_name = :firstName, 
      last_name = :lastName, 
      email = :emailAddress, 
      phone = :phone, 
      workflow_state = :workflow, 
      updated_at = DATETIME()
      WHERE id = :id
    ';

    $statement = $this->pdo->prepare($sql);

    if (empty($statement)) {
      return false;
    }

    $statement->bindValue(':firstName', $shopper->getFirstName(), \PDO::PARAM_STR);
    $statement->bindValue(':lastName', $shopper->getLastName(), \PDO::PARAM_STR);
    $statement->bindValue(':emailAddress', $shopper->getEmailAddress(), \PDO::PARAM_STR);
    $statement->bindValue(':phone', $shopper->getPhone(), \PDO::PARAM_STR);
    $statement->bindValue(':workflow', $shopper->getWorkflowState(), \PDO::PARAM_STR);
    $statement->bindValue(':id', $shopper->getId(), \PDO::PARAM_INT);

    return $statement->execute();
  }

  /**
   * Return data for ALL Shopper's in the table
   *
   * @return array|mixed
   */
  public function getAll() {
    $sql = $this->getShopperSelectSql();

    $statement = $this->pdo->prepare($sql);

    if (empty($statement)) {
      return [];
    }

    return $statement->execute() ? $statement->fetchAll() : [];
  }

  /**
   * Retrieve the data for the shopper with the requested ID
   *
   * @param int $id Tne ID of the shopper that we want to retrieve
   *
   * @return array|mixed
   */
  public function getById(int $id) {
    $sql = $this->getShopperSelectSql();
    $sql .= ' WHERE id = :id';

    $statement = $this->pdo->prepare($sql);

    if (empty($statement)) {
      return [];
    }

    $statement->bindValue(':id', $id, \PDO::PARAM_INT);

    return $statement->execute() ? $statement->fetch() : [];
  }

  /**
   * Retrieve the data for the shopper with the requested email address
   *
   * @param string $emailAddress Tne email address of the shopper that we want to retrieve
   *
   * @return array|mixed
   */
  public function getByEmail(string $emailAddress) {
    $sql = $this->getShopperSelectSql();
    $sql .= ' WHERE email = :emailAddress';

    $statement = $this->pdo->prepare($sql);

    if (empty($statement)) {
      return [];
    }

    $statement->bindValue(':emailAddress', $emailAddress, \PDO::PARAM_STR);

    return $statement->execute() ? $statement->fetch() : [];
  }

  /**
   * The fields that we are looking to return in the select statement. Note SELECT * is almost always EVIL
   *
   * @return string
   */
  protected function getShopperSelectSql() {
    return '
      SELECT 
        id, 
        first_name AS firstName, 
        last_name AS lastName,
        region,
        phone, 
        email AS emailAddress,
        phone_type AS phoneType,
        source,
        over_21 AS over21,
        reason,
        workflow_state AS workflowState,
        created_at AS createdAt,
        updated_at AS updatedAt
      FROM applicants
    ';
  }

}
