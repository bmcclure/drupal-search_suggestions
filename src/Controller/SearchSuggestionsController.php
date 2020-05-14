<?php

namespace Drupal\search_suggestions\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchSuggestionsController extends ControllerBase {

  public function suggest(Request $request, SearchSuggesterInterface $search_suggester) {
    $searcher = $search_suggester->getSearcherPlugin();
    $display = $search_suggester->getDisplayPlugin();
    $form = $search_suggester->getFormPlugin();
    $keys = $form->getInputFromRequest($request);
    $displayConfig = $display->getConfiguration();
    $result = $searcher->search($keys, $displayConfig['result_type']);
    $build = $display->build($result, $searcher);
    $return = Drupal::service('renderer')->render($build);
    return new Response($return);
  }

}
