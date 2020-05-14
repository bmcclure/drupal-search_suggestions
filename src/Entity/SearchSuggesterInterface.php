<?php

namespace Drupal\search_suggestions\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Display\DisplayInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Form\FormInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;

/**
 * Defines the interface for search suggester config entities.
 */
interface SearchSuggesterInterface extends ConfigEntityInterface {

  /**
   * Gets the search suggester machine name.
   *
   * @return string
   *   The machine name.
   */
  public function getId();

  /**
   * Sets the search suggester machine name.
   *
   * @param string $id
   *   The machine name.
   *
   * @return self
   */
  public function setId($id);

  /**
   * Gets the search suggester label.
   *
   * @return string
   *   The label.
   */
  public function getLabel();

  /**
   * Sets the search suggester label.
   *
   * @param string $label
   *   The label.
   *
   * @return self
   */
  public function setLabel($label);

  /**
   * Gets the search suggester heading.
   *
   * @return string
   *   The heading.
   */
  public function getHeading();

  /**
   * Sets the search suggester heading.
   *
   * @param $heading
   *   The new heading.
   *
   * @return self
   */
  public function setHeading($heading);

  /**
   * Gets the associated container entity.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface
   *   The container.
   */
  public function getContainer();

  /**
   * Gets the ID of the container entity.
   *
   * @return string
   *   The container ID.
   */
  public function getContainerId();

  /**
   * Sets the ID of the container.
   *
   * @param $id
   *   The container ID.
   *
   * @return self
   */
  public function setContainerId($id);

  /**
   * Gets the weight of this suggester within the selected container.
   *
   * @return string
   *   The container weight.
   */
  public function getContainerWeight();

  /**
   * Sets the weight of this suggester within the selected container.
   *
   * @param $weight
   *   The container weight.
   *
   * @return self
   */
  public function setContainerWeight($weight);

  /**
   * Gets the current display plugin.
   *
   * @return DisplayInterface
   *   The display plugin.
   */
  public function getDisplayPlugin();

  /**
   * Gets the ID of the display plugin.
   *
   * @return string
   *   The plugin ID.
   */
  public function getDisplayPluginId();

  /**
   * Sets the ID of the display plugin.
   *
   * @param $id
   *   The plugin ID.
   *
   * @return self
   */
  public function setDisplayPluginId($id);

  /**
   * Gets the configuration of the display plugin.
   *
   * @return array
   *   The plugin configuration.
   */
  public function getDisplayConfiguration();

  /**
   * Sets the configuration of the display plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   *
   * @return self
   */
  public function setDisplayConfiguration(array $configuration);

  /**
   * Gets the ID of the form type plugin.
   *
   * @return string
   *   The plugin ID.
   */
  public function getFormPluginId();

  /**
   * Sets the ID of the form type plugin.
   *
   * @param $id
   *   The plugin ID.
   *
   * @return self
   */
  public function setFormPluginId($id);

  /**
   * Gets the configuration of the form type plugin.
   *
   * @return array
   *   The plugin configuration.
   */
  public function getFormConfiguration();

  /**
   * Sets the configuration of the form type plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   *
   * @return self
   */
  public function setFormConfiguration(array $configuration);

  /**
   * Gets the ID of the searcher plugin.
   *
   * @return string
   *   The plugin ID.
   */
  public function getSearcherPluginId();

  /**
   * Sets the ID of the searcher plugin.
   *
   * @param $id
   *   The plugin ID.
   *
   * @return self
   */
  public function setSearcherPluginId($id);

  /**
   * Gets the configuration of the searcher plugin.
   *
   * @return array
   *   The plugin configuration.
   */
  public function getSearcherConfiguration();

  /**
   * Sets the configuration of the searcher plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   *
   * @return self
   */
  public function setSearcherConfiguration(array $configuration);

  /**
   * Gets the form type plugin.
   *
   * @return FormInterface
   *   The form type plugin.
   */
  public function getFormPlugin();

  /**
   * Gets the searcher plugin.
   *
   * @return SearcherInterface
   *   The searcher plugin.
   */
  public function getSearcherPlugin();

  /**
   * Get whether the search suggester is enabled.
   *
   * @return bool
   *   TRUE if the search suggester is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets whether the search suggester is enabled.
   *
   * @param bool $enabled
   *   Whether the search suggester is enabled.
   *
   * @return $this
   */
  public function setEnabled($enabled);

  /**
   * Gets the required libraries to attach for this suggester.
   *
   * @return string[]
   *   An array of required libraries for the suggester.
   */
  public function getAttachedLibraries();

  /**
   * Gets an array of class names to associate with this suggester container.
   *
   * @return string[]
   *   An array of class names.
   */
  public function getClasses();

  /**
   * Gets an array of class names to associate with this suggester search input.
   *
   * @return string[]
   *   An array of class names.
   */
  public function getInputClasses();

}
