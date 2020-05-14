<?php

namespace Drupal\search_suggestions\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines a search suggestions container entity.
 *
 * @ConfigEntityType(
 *   id = "search_suggestions_container",
 *   label = @Translation("Search suggestions container"),
 *   handlers = {
 *     "list_builder" =
 *   "Drupal\search_suggestions\SearchSuggestionsContainerListBuilder",
 *     "storage" =
 *   "Drupal\search_suggestions\SearchSuggestionsContainerStorage",
 *     "form" = {
 *       "add" =
 *   "Drupal\search_suggestions\Form\SearchSuggestionsContainerForm",
 *       "edit" =
 *   "Drupal\search_suggestions\Form\SearchSuggestionsContainerForm",
 *       "delete" =
 *   "Drupal\search_suggestions\Form\SearchSuggestionsContainerDeleteForm"
 *     }
 *   },
 *   config_prefix = "search_suggestions_container",
 *   admin_permission = "administer search_suggestions",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "collection" = "/admin/config/search/suggestions/containers",
 *     "edit-form" =
 *   "/admin/config/search/suggestions/containers/{search_suggestions_container}",
 *     "delete-form" =
 *   "/admin/config/search/suggestions/{search_suggestions_container}/delete"
 *   }
 * )
 */
class SearchSuggestionsContainer extends ConfigEntityBase implements SearchSuggestionsContainerInterface {

  /**
   * The search suggester machine name.
   *
   * @var string
   */
  public $id;

  /**
   * The search suggester label.
   *
   * @var string
   */
  public $label;

  /**
   * The instantiated container plugin.
   *
   * @var \Drupal\search_suggestions\Plugin\SearchSuggestions\Container\ContainerInterface
   */
  public $container;

  /**
   * The container plugin id.
   *
   * @var string
   */
  public $container_plugin_id;

  /**
   * The container plugin configuration.
   *
   * @var array
   */
  public $container_configuration;

  /**
   * The type of this container.
   *
   * @var string
   */
  public $container_type;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerPlugin() {
    return $this->getPlugin('container', 'plugin.manager.search_suggestions_container.container');
  }

  /**
   * @param string $type
   * @param \Drupal\Component\Plugin\PluginManagerInterface|string $manager
   *
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  private function getPlugin($type, $manager) {
    if (!isset($this->{$type})) {
      if (is_string($manager)) {
        $manager = \Drupal::service($manager);
      }

      $this->{$type} = $manager->createInstance($this->get($type . '_plugin_id'), $this->get($type . '_configuration'));
      $this->{$type}->setContextValue('search_suggestions_container', $this);
    }

    return $this->{$type};
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerPluginId() {
    return $this->container_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setContainerPluginId($id) {
    $this->container_plugin_id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerConfiguration() {
    return $this->container_configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setContainerConfiguration(array $configuration) {
    $this->container_configuration = $configuration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerType() {
    return $this->container_type;
  }

  /**
   * {@inheritdoc}
   */
  public function setContainerType($containerType) {
    $this->container_type = $containerType;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    $libraries = [];

    foreach ($this->getContainerPlugin()->getAttachedLibraries() as $library) {
      $libraries[] = $library;
    }

    /** @var \Drupal\search_suggestions\SearchSuggesterManagerInterface $manager */
    $manager = \Drupal::service('search_suggester.manager');
    $suggesters = $this->getSearchSuggesters();
    foreach ($manager->getLibraries($suggesters) as $library) {
      $libraries[] = $library;
    }

    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearchSuggesters() {
    /** @var \Drupal\search_suggestions\SearchSuggesterStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('search_suggester');
    return $storage->loadForContainer($this->id());
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    $this->setContainerType($this->getContainerPlugin()->getContainerType());
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    /** @var \Drupal\search_suggestions\SearchSuggesterStorageInterface $suggesterStorage */
    $suggesterStorage = \Drupal::entityTypeManager()->getStorage('search_suggester');
    $suggesters = $suggesterStorage->loadForContainer($this->id());

    /**
     * @var string $id
     * @var \Drupal\search_suggestions\Entity\SearchSuggesterInterface $suggester
     */
    foreach ($suggesters as $id => $suggester) {
      $build[$id] = $suggester->getDisplayPlugin()->container();
    }

    $build['#attached']['library'] = $this->getAttachedLibraries();

    return $this->getContainerPlugin()->build($build);
  }

}
