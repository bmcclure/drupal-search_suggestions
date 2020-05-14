<?php

namespace Drupal\search_suggestions;

use Drupal;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;

class SearchSuggesterManager implements SearchSuggesterManagerInterface {

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
  public function getSearchSuggesters($includeInactive = FALSE)
  {
    $properties = [];
    if (!$includeInactive) {
      $properties['enabled'] = TRUE;
    }

    return $this->entityTypeManager->getStorage('search_suggester')->loadByProperties($properties);
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchSuggester($id)
  {
    return $this->entityTypeManager->getStorage('search_suggester')->load($id);
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchSuggestersForForm(array &$form, FormStateInterface $formState, $form_id) {
    $suggesters = $this->getSearchSuggesters();

    $results = [];

    foreach ($suggesters as $searchSuggester) {
      $formTypePlugin = $searchSuggester->getFormPlugin();
      if ($formTypePlugin->applies($form, $formState, $form_id)) {
        $results[$searchSuggester->id()] = $searchSuggester;
      }
    }

    return $results;
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(array $searchSuggesters) {
    $libraries = [];

    foreach ($searchSuggesters as $searchSuggester) {
      foreach ($searchSuggester->getAttachedLibraries() as $library) {
        if (!in_array($library, $libraries, TRUE)) {
          $libraries[] = $library;
        }
      }
    }

    return $libraries;
  }

  /**
   * {@inheritDoc}
   */
  public function formAlter(&$form, FormStateInterface $form_state, $form_id)
  {
    $suggesters = $this->getSearchSuggestersForForm($form, $form_state, $form_id);

    foreach ($suggesters as $suggester) {
      $suggester->getFormPlugin()->apply($form, $form_state, $form_id);
    }
  }

}
