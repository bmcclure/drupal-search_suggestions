<?php

namespace Drupal\search_suggestions\Result;

interface ResultInterface {

  /**
   * Gets the result item.
   *
   * @return mixed
   *   The result item.
   */
  public function getResult();

  /**
   * Gets the machine name of this result type.
   *
   * @return string
   *   The result type.
   */
  public function getResultType();
}
