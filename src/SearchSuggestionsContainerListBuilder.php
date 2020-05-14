<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * The list builder for search suggestions container entities.
 */
class SearchSuggestionsContainerListBuilder extends ConfigEntityListBuilder {

  public function buildHeader() {
    $header = [
      'label' => $this->t('Label'),
      'id' => $this->t('Machine name'),
      'type' => $this->t('Type'),
    ];

    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $entity */

    $row = [
      'label' => $entity->label(),
      'id' => $entity->id(),
      'type' => $entity->getContainerPlugin()->label(),
    ];

    return $row + parent::buildRow($entity);
  }

}
