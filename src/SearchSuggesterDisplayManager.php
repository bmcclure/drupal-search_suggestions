<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\search_suggestions\Annotation\SearchSuggesterDisplay;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Display\DisplayInterface;

/**
 * Provides a plugin manager for search suggester display plugins.
 */
class SearchSuggesterDisplayManager extends DefaultPluginManager {

  /**
   * Constructs a new SearchSuggesterDisplayManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/SearchSuggestions/Display', $namespaces, $module_handler, DisplayInterface::class, SearchSuggesterDisplay::class);

    $this->setCacheBackend($cache_backend, 'search_suggester_display');
    $this->alterInfo('search_suggester_display');
  }

  public function getCompatibleDefinitions($searcherId) {
    $definitions = $this->getDefinitions();

    $resultTypes = [];

    if (!empty($searcherId)) {
      /** @var \Drupal\search_suggestions\SearchSuggesterSearcherManager $searcherManager */
      $searcherManager = \Drupal::service('plugin.manager.search_suggester.searcher');
      $searcherDefinition = $searcherManager->getDefinition($searcherId);
      $resultTypes = $searcherDefinition['result_types'];
    }

    $plugins = [];

    foreach ($definitions as $id => $definition) {
      $compatibleTypes = array_intersect($definition['result_types'], $resultTypes);

      if (!empty($compatibleTypes)) {
        $plugins[$id] = $definition;
      }
    }

    return $plugins;
  }

}
