<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Config\Entity\ConfigEntityStorage;

/**
 * Defines the Search Suggester storage.
 */
class SearchSuggesterStorage extends ConfigEntityStorage implements SearchSuggesterStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadValid() {
    $query = $this->getQuery()
      ->condition('enabled', 1);

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    return $this->loadMultiple($result);
  }

  /**
   * {@inheritdoc}
   */
  public function loadForContainer($containerId) {
    $query = $this->getQuery()
      ->condition('enabled', 1)
      ->condition('container_id', $containerId)
      ->sort('container_weight');

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    return $this->loadMultiple($result);
  }
}
