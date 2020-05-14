<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Container;

use Drupal\search_suggestions\Annotation\SearchSuggestionsContainer;

/**
 * Provides an 'Search input' container plugin.
 *
 * @SearchSuggestionsContainer(
 *   id = "search_input",
 *   label = @Translation("Search input"),
 *   description = @Translation("A container appended to the search input that appears below it while typing."),
 *   container_type = "form",
 *   context = {
 *     "search_suggestions_container" = @ContextDefinition("entity:search_suggestions_container", label = @Translation("Search suggestions container"))
 *   }
 * )
 *
 */
class SearchInputContainer extends FormContainerBase {

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
    $classes[] = 'search-suggestions-container--search-input';
    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    $libraries = parent::getAttachedLibraries();
    $libraries[] = 'search_suggestions/search_suggestions_container_search_input';
    return $libraries;
  }

}
