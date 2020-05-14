<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Display;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;
use Drupal\search_suggestions\Result\ResultInterface;

/**
 * Represents a plugin for search suggester display types.
 */
interface DisplayInterface extends ConfigurablePluginInterface, ContainerFactoryPluginInterface, ContextAwarePluginInterface, PluginFormInterface {

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
   * Retrieves the result types supported by this display plugin.
   *
   * @return string[]
   */
  public function getResultTypes();

  /**
   * Displays the provided results.
   *
   * @param $result
   *   The results to display, type is dependent on the searcher and display type.
   * @param SearcherInterface $searcher
   *   The searcher plugin.
   *
   * @return array
   *   The built render array.
   */
  public function build(ResultInterface $result, SearcherInterface $searcher);

  /**
   * Gets a build array for the results container that will render on page load.
   *
   * @return array
   *   The built render array.
   */
  public function container();

  /**
   * Gets the libraries required for this display.
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
