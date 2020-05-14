<?php

namespace Drupal\search_suggestions_autocomplete;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Drupal\search_suggestions_autocomplete\AutocompleteBuilder\DefaultAutocompleteBuilder;
use Drupal\search_suggestions_autocomplete\Event\AutocompleteBuilderTypesEvent;

class AutocompleteBuilderManager {

  use StringTranslationTrait;

  /**
   * Gets an array of all known autocomplete builder types and their classes.
   *
   * @return array[]
   *   An array of result type definitions
   */
  public function getAutocompleteBuilderTypes() {
    $autocompleteBuilderTypes = [
      'default' => [
        'title' => $this->t('Default'),
        'class' => DefaultAutocompleteBuilder::class,
      ],
      //'jquery_ui_autocomplete' => [
      //  'title' => $this->t('jQuery UI Autocomplete'),
      //  'class' => JqueryUiAutocompleteBuilder::class,
      //],
    ];

    $event = new AutocompleteBuilderTypesEvent($autocompleteBuilderTypes);
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
    $eventDispatcher = \Drupal::service('event_dispatcher');
    $eventDispatcher->dispatch(AutocompleteBuilderTypesEvent::EVENT_NAME, $event);

    return $event->autocompleteBuilderTypes;
  }

  /**
   * @param $id
   *
   * @return array
   *
   */
  public function getAutocompleteBuilderType($id) {
    $autocompleteBuilderType = NULL;
    $autocompleteBuilderTypes = $this->getAutocompleteBuilderTypes();

    if (array_key_exists($id, $autocompleteBuilderTypes)) {
      $autocompleteBuilderType = $autocompleteBuilderTypes[$id];
    }

    return $autocompleteBuilderType;
  }

  /**
   * @param $id
   * @param \Drupal\search_suggestions\Entity\SearchSuggesterInterface $searchSuggester
   *
   * @return \Drupal\search_suggestions_autocomplete\AutocompleteBuilder\AutocompleteBuilderInterface|NULL
   */
  public function getAutocompleteBuilder($id, SearchSuggesterInterface $searchSuggester) {
    $type = $this->getAutocompleteBuilderType($id);
    $builder = NULL;
    if ($type) {
      $builder = new $type['class']($searchSuggester);
    }
    return $builder;
  }

}
