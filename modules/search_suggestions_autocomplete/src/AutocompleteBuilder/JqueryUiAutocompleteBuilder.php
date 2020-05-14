<?php

namespace Drupal\search_suggestions_autocomplete\AutocompleteBuilder;

use Drupal\search_suggestions_autocomplete\Result\AutocompleteResultInterface;
use Drupal\search_suggestions_autocomplete\AutocompleteItem\AutocompleteItemInterface;

class JqueryUiAutocompleteBuilder extends AutocompleteBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    $libraries = parent::getLibraries();
    $libraries[] = 'search_suggestions_autocomplete/jquery_ui_autocomplete';
    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function buildAjaxResponse(AutocompleteResultInterface $result) {
    $items = (array) $result->getResult();
    $response = [];

    /** @var AutocompleteItemInterface $item */
    foreach ($items as $item) {
      $response[] = [
        'label' => $item->getSuggestedInput(),
        'value' => $item->getSuggestedInput()
      ];
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function buildList(AutocompleteResultInterface $result) {
    $items = (array) $result->getResult();
    $build = [
      'type' => 'container',
      'attributes' => [
        'class' => 'search-suggestions-jquery-ui-autocomplete-container',
      ]
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {
    return ['search-suggester-jquery-ui-autocomplete'];
  }

}
