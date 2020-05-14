<?php

namespace Drupal\search_suggestions\Result;

use Drupal\Core\Entity\EntityInterface;

class EntityListResult extends IterableResultBase {

  /**
   * EntityListResult constructor.
   *
   * @param EntityInterface[] $entities
   */
  public function __construct(array $entities) {
    parent::__construct('entity_list');
    $this->result = $entities;
  }

}
