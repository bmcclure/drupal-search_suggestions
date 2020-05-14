<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Form;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Represents a plugin for search suggester form types.
 */
interface FormInterface extends ConfigurablePluginInterface, ContainerFactoryPluginInterface, ContextAwarePluginInterface, PluginFormInterface {

  /**
   * Retrieves the plugin's label.
   *
   * @return string
   *   The plugin's human-readable and translated label.
   */
  public function label();

  /**
   * Retrieves the plugin's description.
   *
   * @return string|null
   *   The plugin's translated description; or NULL if it has none.
   */
  public function getDescription();

  /**
   * Check if this form type plugin instance applies to the provided form.
   *
   * @param array $form
   *   The form.
   * @param FormStateInterface $formState
   *   The form state.
   * @param $formId
   *   The form ID.
   *
   * @return bool
   *   TRUE if this form type plugin instance applies to the form, FALSE otherwise.
   */
  public function applies(array &$form, FormStateInterface $formState, $formId);

  /**
   * Applies the search suggester to the provided form.
   *
   * @param array $form
   *   The form.
   * @param FormStateInterface $formState
   *   The form state.
   * @param $formId
   *   The form ID.
   *
   * @return void
   */
  public function apply(array &$form, FormStateInterface $formState, $formId);

  /**
   * Gets the input from this form field from the provided request if available.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object to get the input from.
   *
   * @return string
   *   The search input, if available, or an empty string.
   */
  public function getInputFromRequest(Request $request);

  /**
   * Gets the libraries required for this form type.
   *
   * @return string[]
   *   The required libraries.
   */
  public function getAttachedLibraries();

  /**
   * Gets an array of classes to associate with the suggester container.
   *
   * @return string[]
   *   An array of class names.
   */
  public function getClasses();

  /**
   * Gets an array of classes to associate with the search input.
   *
   * @return string[]
   *   An array of class names.
   */
  public function getInputClasses();

}
