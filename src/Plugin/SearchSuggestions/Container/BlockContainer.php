<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Container;

use Drupal\search_suggestions\Annotation\SearchSuggestionsContainer;

/**
 * Provides an 'Search input' container plugin.
 *
 * @SearchSuggestionsContainer(
 *   id = "block",
 *   label = @Translation("Block"),
 *   description = @Translation("A container that offers a custom block to place."),
 *   container_type = "block",
 *   context = {
 *     "search_suggestions_container" = @ContextDefinition("entity:search_suggestions_container", label = @Translation("Search suggestions container"))
 *   }
 * )
 *
 */
class BlockContainer extends BlockContainerBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$content) {
    $build = $this->getResultsContainer();
    $build['inner'] = array_merge($build['inner'], $content);
    return $build;
  }

  protected function getResultsContainerClasses() {
    $classes = parent::getResultsContainerClasses();
    $classes[] = 'search-suggestions-container--block';
    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    $libraries = parent::getAttachedLibraries();
    $libraries[] = 'search_suggestions/search_suggestions_container_block';
    return $libraries;
  }

}
