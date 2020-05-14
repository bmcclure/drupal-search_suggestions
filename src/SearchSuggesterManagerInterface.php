<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface;

interface SearchSuggesterManagerInterface {

  /**
   * Gets a array of active (or all) search suggesters.
   *
   * @param bool $includeInactive
   *   Include inactive search suggesters in the result.
   *
   * @return SearchSuggesterInterface[]
   *   The search suggester entities.
   */
  public function getSearchSuggesters($includeInactive = FALSE);

  /**
   * Gets a single search suggester by ID.
   *
   * @param $id
   *   The search suggester ID.
   *
   * @return SearchSuggesterInterface|NULL
   *   The search suggester entity, or NULL if not found.
   */
  public function getSearchSuggester($id);

  /**
   * Gets all search suggesters which match the provided form.
   *
   * @param array $form
   *   The form.
   * @param FormStateInterface $formState
   *   The form state.
   * @param $form_id
   *   The form ID.
   *
   * @return SearchSuggesterInterface[]
   */
  public function getSearchSuggestersForForm(array &$form, FormStateInterface $formState, $form_id);

  /**
   * Gets the search suggesters for the provided container ID.
   *
   * @param array $searchSuggesters
   *   The search suggesters.
   *
   * @return string[]
   *   The attached libraries.
   */
  public function getLibraries(array $searchSuggesters);

  /**
   * Alters the provided form by applying form-based suggesters to it.
   *
   * @param $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param $form_id
   *   The form ID.
   *
   * @return void
   */
  public function formAlter(&$form, FormStateInterface $form_state, $form_id);

}
