<?php

namespace Drupal\search_suggestions;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\search_suggestions\Event\ResultTypesEvent;
use Drupal\search_suggestions\Result\CountResult;
use Drupal\search_suggestions\Result\EntityListResult;
use Drupal\search_suggestions\Result\ViewResult;

class ResultTypeManager {

  use StringTranslationTrait;

  /**
   * Gets an array of all known result types and their classes.
   *
   * @return array[]
   *   An array of result type definitions
   */
  public function getResultTypes() {
    $resultTypes = [
      'entity_list' => [
        'title' => $this->t('Entity list'),
        'class' => EntityListResult::class,
      ],
      'view' => [
        'title' => $this->t('View'),
        'class' => ViewResult::class,
      ],
      'count' => [
        'title' => $this->t('Count'),
        'class' => CountResult::class,
      ],
    ];

    $event = new ResultTypesEvent($resultTypes);
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
    $eventDispatcher = \Drupal::service('event_dispatcher');
    $eventDispatcher->dispatch(ResultTypesEvent::EVENT_NAME, $event);
    return $event->resultTypes;
  }

  /**
   * @param $id
   *
   * @return string
   *
   */
  public function getResultType($id) {
    $resultType = NULL;
    $resultTypes = $this->getResultTypes();

    if (array_key_exists($id, $resultTypes)) {
      $resultType = $resultTypes[$id];
    }

    return $resultType;
  }
}
