<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Represents a plugin for search suggester searcher types.
 */
interface SearcherInterface extends ConfigurablePluginInterface, ContainerFactoryPluginInterface, ContextAwarePluginInterface, PluginFormInterface {

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
   * Retrieves the types of results supported by this searcher.
   *
   * @return string[]
   */
  public function getResultTypes();

  /**
   * Displays the provided results.
   *
   * @param array|string $input
   *   The search input, either a single string or a keyed array of values for the searcher.
   * @param string $resultType
   *   The result type (eg. entity_list, count, autocomplete)
   *
   * @return \Drupal\search_suggestions\Result\ResultInterface
   *   The result of the search query.
   */
  public function search($input, $resultType);

  /**
   * Gets the libraries required for this searcher.
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
