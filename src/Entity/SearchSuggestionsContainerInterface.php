<?php

namespace Drupal\search_suggestions\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Display\DisplayInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Form\FormInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;

/**
 * Defines the interface for search suggester container config entities.
 */
interface SearchSuggestionsContainerInterface extends ConfigEntityInterface {

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
   * Checks if this container has an associated block.
   *
   * @return string
   *   The container type.
   */
  public function getContainerType();

  /**
   * Sets the container type of this container.
   *
   * @param string $containerType
   *   The new container type.
   *
   * @return self
   */
  public function setContainerType($containerType);

  /**
   * Gets the current container plugin.
   *
   * @return \Drupal\search_suggestions\Plugin\SearchSuggestions\Container\ContainerInterface
   *   The container plugin.
   */
  public function getContainerPlugin();

  /**
   * Gets the ID of the container plugin.
   *
   * @return string
   *   The plugin ID.
   */
  public function getContainerPluginId();

  /**
   * Sets the ID of the container plugin.
   *
   * @param $id
   *   The plugin ID.
   *
   * @return self
   */
  public function setContainerPluginId($id);

  /**
   * Gets the configuration of the container plugin.
   *
   * @return array
   *   The plugin configuration.
   */
  public function getContainerConfiguration();

  /**
   * Sets the configuration of the container plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   *
   * @return self
   */
  public function setContainerConfiguration(array $configuration);

  /**
   * Gets the required libraries to attach for this suggester.
   *
   * @return string[]
   *   An array of required libraries for the suggester.
   */
  public function getAttachedLibraries();

  /**
   * Builds this container.
   *
   * @return array
   *   A render array for this container with the provided suggesters.
   */
  public function build();

  /**
   * Get all search suggesters associated with this container.
   *
   * @return \Drupal\search_suggestions\Entity\SearchSuggesterInterface[]
   */
  public function getSearchSuggesters();

}
