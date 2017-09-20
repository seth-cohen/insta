<?php

namespace IC\DAOs;

interface ShopperDAOInterface {

  public function save(\IC\Models\ShopperModel $shopper);

  public function getAll();

  public function getById(int $id);

  public function getByEmail(string $emailAddress);
}
