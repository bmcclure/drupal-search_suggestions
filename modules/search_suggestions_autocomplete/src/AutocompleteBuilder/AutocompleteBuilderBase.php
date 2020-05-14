<?php

namespace Drupal\search_suggestions_autocomplete\AutocompleteBuilder;

use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Drupal\search_suggestions_autocomplete\Result\AutocompleteResultInterface;
use Drupal\search_suggestions_autocomplete\AutocompleteItem\AutocompleteItemInterface;

abstract class AutocompleteBuilderBase implements AutocompleteBuilderInterface {

  /** @var \Drupal\search_suggestions\Entity\SearchSuggesterInterface */
  protected $searchSuggester;

  public function __construct(SearchSuggesterInterface $searchSuggester) {
    $this->searchSuggester = $searchSuggester;
  }

  /**
   * {@inheritdoc}
   */
  public function buildList(AutocompleteResultInterface $result) {
    $items = (array) $result->getResult();
    $build = [];
    $buildItems = [];

    foreach ($items as $item) {
      $buildItem = $this->buildItem($item);

      if (!empty($buildItem)) {
        $buildItems[] = $buildItem;
      }
    }

    if (!empty($buildItems)) {
      $build = $buildItems;
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildItem(AutocompleteItemInterface $item) {
    $build = [];

    $buildItem = $this->buildSingleItem($item);

    if (!empty($buildItem)) {
      $build = $buildItem;
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    return [];
  }

  protected function buildSingleItem(AutocompleteItemInterface $item) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildAjaxResponse(AutocompleteResultInterface $result) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getInputClasses() {
    return [];
  }

}
