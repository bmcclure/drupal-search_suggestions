<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Container;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Represents a plugin for search suggester display types.
 */
interface ContainerInterface extends ConfigurablePluginInterface, ContainerFactoryPluginInterface, ContextAwarePluginInterface, PluginFormInterface {

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
   * Retrieves the container type.
   *
   * @return string
   *   The container type.
   */
  public function getContainerType();

  /**
   * Displays the container.
   *
   * @param array $content
   *   The search suggester output to show in the container.
   *
   * @return array
   *   The built render array.
   */
  public function build(array &$content);

  /**
   * For containers of type "form", this can be used to attach the container to
   * any form.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param string $form_id
   *   The form ID.
   *
   * @return void
   */
  public function formAlter(array &$form, FormStateInterface $form_state, $form_id);

  /**
   * Gets the libraries required for this container.
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
