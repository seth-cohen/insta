<?php

namespace IC\Services;

class ShopperService {

  public function __construct(\IC\DAOs\ShopperDAOInterface $shopperDAO) {
    $this->shopperDAO = $shopperDAO;
  }

  /**
   *
   * @param array $data
   *
   * @todo Create a value object to represent the data needed to create a valid ShopperModel
   */
  public function create(array $data) {
    $model = \IC\Models\ShopperModel::create($data['firstName'], $data['lastName'], $data['emailAddress']);

    $id = null;
    if ($model->validate()) {
      $id = $this->shopperDAO->save($model);
      $model->populate(['id' => $id]);
    }

    return $model;
  }

  /**
   * @param string $email
   *
   * @return \IC\Models\ShopperModel|null
   */
  public function getShopperByEmail(string $email) {
    $data = $this->shopperDAO->getByEmail($email);
    if (!empty($data)) {
      $model = new \IC\Models\ShopperModel();
      $model->populate($data);
    }

    return $model ?? null;
  }
}
