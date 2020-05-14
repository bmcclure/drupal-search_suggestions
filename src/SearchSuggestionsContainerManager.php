<?php

namespace Drupal\search_suggestions;

use Drupal;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;

class SearchSuggestionsContainerManager implements SearchSuggestionsContainerManagerInterface {

  /** @var EntityTypeManagerInterface */
  protected $entityTypeManager;

  /**
   * SearchSuggesterManager constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainers()
  {
    $properties = [];
    return $this->entityTypeManager->getStorage('search_suggestions_container')->loadByProperties($properties);
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerOptions($emptyOption = NULL) {
    $containers = $this->getContainers();
    $options = [];

    if (!is_null($emptyOption)) {
      $options[''] = $emptyOption;
    }

    foreach ($containers as $container) {
      $options[$container->id()] = $container->label();
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainer($id)
  {
    return $this->entityTypeManager->getStorage('search_suggestions_container')->load($id);
  }

  /**
   * {@inheritDoc}
   */
  public function formAlter(&$form, FormStateInterface $form_state, $form_id)
  {
    /** @var \Drupal\search_suggestions\SearchSuggestionsContainerStorageInterface $storage */
    $storage = Drupal::entityTypeManager()->getStorage('search_suggestions_container');
    $containers = $storage->loadByType('form');

    foreach ($containers as $container) {
      $container->getContainerPlugin()->formAlter($form, $form_state, $form_id);
    }
  }

}
