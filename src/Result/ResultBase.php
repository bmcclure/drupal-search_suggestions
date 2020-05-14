<?php

namespace Drupal\search_suggestions\Result;

abstract class ResultBase implements ResultInterface {

  protected $result;

  protected $resultType;

  protected function __construct($resultType) {
    $this->resultType = $resultType;
  }

  /**
   * {@inheritdoc}
   */
  public function getResult() {
    return $this->result;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultType() {
    return $this->resultType;
  }

}
