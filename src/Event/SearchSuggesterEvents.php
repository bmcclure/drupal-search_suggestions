<?php

namespace Drupal\search_suggestions\Event;

final class SearchSuggesterEvents {

  /**
   * Name of the event fired after loading a search suggester.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_LOAD = 'search_suggester.load';

  /**
   * Name of the event fired after creating a new search suggester.
   *
   * Fired before the search suggester is saved.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_CREATE = 'search_suggester.create';

  /**
   * Name of the event fired before saving a search suggester.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_PRESAVE = 'search_suggester.presave';

  /**
   * Name of the event fired after saving a new search suggester.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_INSERT = 'search_suggester.insert';

  /**
   * Name of the event fired after saving an existing search suggester.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_UPDATE = 'search_suggester.update';

  /**
   * Name of the event fired before deleting a search suggester.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_PREDELETE = 'search_suggester.predelete';

  /**
   * Name of the event fired after deleting a search suggester.
   *
   * @Event
   *
   * @see \Drupal\search_suggestions\Event\SearchSuggesterEvent
   */
  const SEARCH_SUGGESTER_DELETE = 'search_suggester.delete';

}
