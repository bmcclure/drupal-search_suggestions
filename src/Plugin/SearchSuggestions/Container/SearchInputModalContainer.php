<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Container;

use Drupal\search_suggestions\Annotation\SearchSuggestionsContainer;

/**
 * Provides an 'Search input (modal)' container plugin.
 *
 * @SearchSuggestionsContainer(
 *   id = "search_input_modal",
 *   label = @Translation("Search input (modal)"),
 *   description = @Translation("A container that wraps the search input and dims the rest of the website."),
 *   container_type = "form",
 *   context = {
 *     "search_suggestions_container" = @ContextDefinition("entity:search_suggestions_container", label = @Translation("Search suggestions container"))
 *   }
 * )
 *
 */
class SearchInputModalContainer extends SearchInputContainer {

  /**
   * {@inheritdoc}
   */
  public function build(array &$content) {
    $build = [];
    $build['background'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'search-suggestions-container-background',
      ],
    ];
    $build['container'] = parent::build($content);
    return $build;
  }

  protected function attachContainerToFormField(&$form, $fieldName) {
    $before = !empty($form[$fieldName]['#prefix']) ? $form[$fieldName]['#prefix'] : '';
    $before .= $this->renderFormElement();
    $form[$fieldName]['#prefix'] = $before;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    $libraries = parent::getAttachedLibraries();
    $libraries[] = 'search_suggestions/search_suggestions_container_search_input_modal';
    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultsContainer() {
    $container = parent::getResultsContainer();

    $path = drupal_get_path('module', 'search_suggestions');
    // @todo turn path into full URI

    $inner = $container['inner'];
    unset($container['inner']);
    $container['close'] = [
      '#theme' => 'image',
      '#uri' =>"$path/images/close-icon.svg",
      '#attributes' => [
        'class' => ['search-suggestions-container-close-icon'],
        'alt' => $this->t('Close suggestions'),
      ]
    ];
    $container['inner'] = $inner;

    return $container;
  }

  /**
   * {@inheritdoc}
   */
  protected function getResultsContainerClasses() {
    $classes = parent::getResultsContainerClasses();
    $classes[] = 'search-suggestions-container--search-input-modal';
    return $classes;
  }

}
