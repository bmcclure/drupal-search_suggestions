<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;

/**
 * Defines the interface for search suggestions container storage.
 */
interface SearchSuggestionsContainerStorageInterface extends ConfigEntityStorageInterface {

  /**
   * Loads all valid search suggestions containers of the provided type.
   *
   * @param string $containerType
   *   The container type.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface[]
   *   The search suggestions containers.
   */
  public function loadByType($containerType);

}
