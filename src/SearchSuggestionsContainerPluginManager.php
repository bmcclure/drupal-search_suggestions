<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\search_suggestions\Annotation\SearchSuggestionsContainer;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Container\ContainerInterface;

/**
 * Provides a plugin manager for search suggester display plugins.
 */
class SearchSuggestionsContainerPluginManager extends DefaultPluginManager {

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
    parent::__construct('Plugin/SearchSuggestions/Container', $namespaces, $module_handler, ContainerInterface::class, SearchSuggestionsContainer::class);

    $this->setCacheBackend($cache_backend, 'search_suggester_container');
    $this->alterInfo('search_suggester_container');
  }

  public function getCompatibleDefinitions($searcherId) {
    $definitions = $this->getDefinitions();

    $plugins = [];

    foreach ($definitions as $id => $definition) {
      $plugins[$id] = $definition;
    }

    return $plugins;
  }

}
