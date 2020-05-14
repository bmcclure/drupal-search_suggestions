<?php

namespace Drupal\search_suggestions\Event;

use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the search suggester event.
 *
 * @see \Drupal\search_suggestions\Event\SearchSuggesterEvents
 */
class SearchSuggesterEvent extends Event {

  /**
   * The search suggester.
   *
   * @var \Drupal\search_suggestions\Entity\SearchSuggesterInterface
   */
  protected $searchSuggester;

  /**
   * Constructs a new SearchSuggesterEvent.
   *
   * @param \Drupal\search_suggestions\Entity\SearchSuggesterInterface $searchSuggester
   *   The search suggester.
   */
  public function __construct(SearchSuggesterInterface $searchSuggester) {
    $this->searchSuggester = $searchSuggester;
  }

  /**
   * Gets the search suggester.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggesterInterface
   *   The search suggester.
   */
  public function getSearchSuggester() {
    return $this->searchSuggester;
  }

}
