<?php

namespace Drupal\search_suggestions_autocomplete\Result;

use Drupal\search_suggestions\Result\ResultBase;
use Drupal\search_suggestions_autocomplete\AutocompleteItem\AutocompleteItemInterface;

class AutocompleteResult extends ResultBase implements AutocompleteResultInterface {

  /**
   * AutocompleteListResult constructor.
   *
   * @param AutocompleteItemInterface[] $items
   */
  public function __construct(array $items) {
    parent::__construct('autocomplete');
    $this->result = $items;
  }

}
