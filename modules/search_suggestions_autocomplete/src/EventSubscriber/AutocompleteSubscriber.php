<?php

namespace Drupal\search_suggestions_autocomplete\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\search_suggestions\Event\ResultTypesEvent;
use Drupal\search_suggestions_autocomplete\Result\AutocompleteResult;

class AutocompleteSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      ResultTypesEvent::EVENT_NAME => 'resultTypes',
    ];
  }

  public function resultTypes(ResultTypesEvent $event) {
    $event->resultTypes['autocomplete'] = [
      'title' => $this->t('Autocomplete'),
      'class' => AutocompleteResult::class,
    ];
  }
}
