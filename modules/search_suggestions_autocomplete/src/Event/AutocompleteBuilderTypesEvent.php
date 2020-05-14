<?php

namespace Drupal\search_suggestions_autocomplete\Event;

use Symfony\Component\EventDispatcher\Event;

class AutocompleteBuilderTypesEvent extends Event {

  public const EVENT_NAME = 'search_suggestions_autocomplete_builder_types';

  /**
   * The array of known result types
   *
   * @var array
   */
  public $autocompleteBuilderTypes;

  public function __construct($autocompleteBuilderTypes = []) {
    $this->autocompleteBuilderTypes = $autocompleteBuilderTypes;
  }
}
