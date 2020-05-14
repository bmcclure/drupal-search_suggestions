<?php

namespace Drupal\search_suggestions\Result;

class CountResult extends ResultBase {

  /**
   * CountResult constructor.
   *
   * @param int $count
   */
  public function __construct(int $count) {
    parent::__construct('count');
    $this->result = $count;
  }

}
