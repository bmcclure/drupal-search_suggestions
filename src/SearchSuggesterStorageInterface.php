<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;

/**
 * Defines the interface for search suggester storage.
 */
interface SearchSuggesterStorageInterface extends ConfigEntityStorageInterface {

  /**
   * Loads the valid search suggester config entities.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggesterInterface[]
   *   The valid search suggesters.
   */
  public function loadValid();

  /**
   * Loads the valid search suggester config entities related to the container.
   *
   * @param $containerId
   *   The container ID.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggesterInterface[]
   *   The valid search suggesters.
   */
  public function loadForContainer($containerId);

}
