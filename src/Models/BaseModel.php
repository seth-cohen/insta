<?php

namespace IC\Models;

abstract class BaseModel {
  /**
   * Populates this model from a key/value map whose keys match up to the property names of this model. This makes
   * sense to have this function in the base class so that all models can use data provided from repository
   *
   * @param array $map a map whose keys match up to the property names of this model.
   *
   * @return \IC\Models\BaseModel This model for method chaining.
   */
  public function populate($map) {
    /* Set all properties from the map. We do array_keys + get_object_vars so that we aren't assigning the values to an
     * unused iterator variable (e.g. foreach($this as $key => $value) where $value is never used). */
    foreach (array_keys(get_object_vars($this)) as $key) {
      if (isset($map[$key])) {
        $this->$key = $map[$key];
      }
    }

    return $this;
  }

  /**
   * Run all configured validations against the data in the model's properties and set any validation errors
   *
   * @return bool
   */
  public function validate() {
    return true;
  }
}
