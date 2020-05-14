<?php

namespace Drupal\search_suggestions\Event;

use Symfony\Component\EventDispatcher\Event;

class ResultTypesEvent extends Event {

  public const EVENT_NAME = 'search_suggestions_result_types';

  /**
   * The array of known result types
   *
   * @var array
   */
  public $resultTypes;

  public function __construct($resultTypes = []) {
    $this->resultTypes = $resultTypes;
  }
}
