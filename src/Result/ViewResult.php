<?php

namespace Drupal\search_suggestions\Result;

use Drupal\views\ViewExecutable;

class ViewResult extends ResultBase {

  /**
   * ViewResult constructor.
   *
   * @param ViewExecutable $view
   */
  public function __construct(ViewExecutable $view) {
    parent::__construct('view');
    $this->result = $view;
  }

}
