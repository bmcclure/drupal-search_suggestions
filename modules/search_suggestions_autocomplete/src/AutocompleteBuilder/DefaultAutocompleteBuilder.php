<?php

namespace Drupal\search_suggestions_autocomplete\AutocompleteBuilder;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\search_suggestions_autocomplete\Result\AutocompleteResultInterface;
use Drupal\search_suggestions_autocomplete\AutocompleteItem\AutocompleteItemInterface;

class DefaultAutocompleteBuilder extends AutocompleteBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    $libraries = parent::getLibraries();
    $libraries[] = 'search_suggestions_autocomplete/search_suggestions_autocomplete';
    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function buildList(AutocompleteResultInterface $result) {
    $items = (array) $result->getResult();
    $build = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#attributes' => [
        'class' => ['search-suggester-autocomplete-result'],
      ],
      '#items' => [],
    ];

    foreach ($items as $item) {
      $buildItem = $this->buildItem($item);

      if (!empty($buildItem)) {
        $build['#items'][] = $buildItem;
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildSingleItem(AutocompleteItemInterface $item) {
    $build = $this->buildLink($item);
    $build['#wrapper_attributes']['class'][] = 'search-suggester-autocomplete-item';
    return $build;
  }

  private function buildLink(AutocompleteItemInterface $item) {
    $plugin = $this->searchSuggester->getDisplayPlugin();
    $configuration = $plugin->getConfiguration();
    $searchPath = $configuration['search_path'] ?? '/';
    $searchPath .= rawurlencode($item->getSuggestedInput());
    $directSearch = $configuration['direct_search'] ?? FALSE;
    $url = Url::fromUserInput($searchPath);
    $title = $item->getSuggestedInput();
    $link = Link::fromTextAndUrl($title, $url);
    $build = $link->toRenderable();
    $build['#attributes']['class'][] = 'search-suggester-autocomplete-link';

    if ($directSearch) {
      $build['#attributes']['class'][] = 'js-direct-search';
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {
    return ['search-suggester-autocomplete'];
  }

}
