<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\search_suggestions\Annotation\SearchSuggesterSearcher;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;

/**
 * Provides a plugin manager for search suggester display plugins.
 */
class SearchSuggesterSearcherManager extends DefaultPluginManager {

  /**
   * Constructs a new SearchSuggesterSearcherManager.
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
    parent::__construct('Plugin/SearchSuggestions/Searcher', $namespaces, $module_handler, SearcherInterface::class, SearchSuggesterSearcher::class);

    $this->setCacheBackend($cache_backend, 'search_suggester_searcher');
    $this->alterInfo('search_suggester_searcher');
  }

}
