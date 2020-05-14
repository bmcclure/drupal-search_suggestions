<?php

namespace Drupal\search_suggestions;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;

interface SearchSuggestionsContainerManagerInterface {

  /**
   * Gets a array of all search suggestions containers.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface[]
   *   The search suggestions container entities.
   */
  public function getContainers();

  /**
   * Gets an array of container options suitable for a select field.
   *
   * @param string|NULL $emptyOption
   *   The label of the empty option, or NULL (the default) to not have an
   *   empty option.
   *
   * @return string[]
   *   The container options array.
   */
  public function getContainerOptions($emptyOption = NULL);

  /**
   * Gets a single search suggestions container by ID.
   *
   * @param $id
   *   The search suggestions container ID.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface|NULL
   *   The search suggestions container entity, or NULL if not found.
   */
  public function getContainer($id);

  /**
   * Alters the provided form by applying form-based containers to it.
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
