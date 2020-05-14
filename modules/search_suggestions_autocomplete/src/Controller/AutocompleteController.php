<?php

namespace Drupal\search_suggestions_autocomplete\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;

class AutocompleteController extends ControllerBase {

  /**
   * Handle an AJAX request for autocomplete results. Expects the search keys
   * to be in the request.
   *
   * @param \Drupal\search_suggestions\Entity\SearchSuggesterInterface $search_suggester
   *   The search suggester.
   *
   * @param string $input
   *   The search input.
   *
   * @return array
   */
  public function autocomplete(SearchSuggesterInterface $search_suggester, $input) {
    $config = $search_suggester->getDisplayPlugin()->getConfiguration();
    /** @var \Drupal\search_suggestions_autocomplete\AutocompleteBuilderManager $builderManager */
    $builderManager = \Drupal::service('search_suggestions_autocomplete.builder_manager');
    $builder = $builderManager->getAutocompleteBuilder($config['builder'], $search_suggester);
    $response = [];

    if ($builder) {
      $searcher = $search_suggester->getSearcherPlugin();
      $result = $searcher->search($input, 'autocomplete');
      $response = $builder->buildAjaxResponse($result);
    }

    return $response;
  }

}
