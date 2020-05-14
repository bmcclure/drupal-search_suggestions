<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;

/**
 * The list builder for search suggester entities.
 */
class SearchSuggesterListBuilder extends ConfigEntityListBuilder {

  public function buildHeader() {
    $header = [
      'label' => $this->t('Label'),
      'id' => $this->t('Machine name'),
      'enabled' => $this->t('Enabled'),
    ];

    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity) {
    /** @var SearchSuggesterInterface $entity */

    $row = [
      'label' => $entity->label(),
      'id' => $entity->id(),
      'enabled' => $entity->isEnabled() ? $this->t('Yes') : $this->t('No'),
    ];

    return $row + parent::buildRow($entity);
  }

}
