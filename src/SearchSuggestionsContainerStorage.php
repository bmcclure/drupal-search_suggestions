<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Config\Entity\ConfigEntityStorage;

/**
 * Defines the Search Suggester storage.
 */
class SearchSuggestionsContainerStorage extends ConfigEntityStorage implements SearchSuggestionsContainerStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadByType($containerType) {
    $query = $this->getQuery()
      ->condition('container_type', $containerType);

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    return $this->loadMultiple($result);
  }

}
