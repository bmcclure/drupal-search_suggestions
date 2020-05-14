<?php

namespace Drupal\search_suggestions\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Display\DisplayInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Form\FormInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;

/**
 * Defines a search suggester entity.
 *
 * @ConfigEntityType(
 *   id = "search_suggester",
 *   label = @Translation("Search suggester"),
 *   handlers = {
 *     "list_builder" = "Drupal\search_suggestions\SearchSuggesterListBuilder",
 *     "storage" = "Drupal\search_suggestions\SearchSuggesterStorage",
 *     "form" = {
 *       "add" = "Drupal\search_suggestions\Form\SearchSuggesterForm",
 *       "edit" = "Drupal\search_suggestions\Form\SearchSuggesterForm",
 *       "delete" = "Drupal\search_suggestions\Form\SearchSuggesterDeleteForm"
 *     }
 *   },
 *   config_prefix = "search_suggester",
 *   admin_permission = "administer search_suggestions",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "collection" = "/admin/config/search/suggestions/suggesters",
 *     "edit-form" = "/admin/config/search/suggestions/suggesters/{search_suggester}",
 *     "delete-form" = "/admin/config/search/suggestions/suggesters/{search_suggester}/delete"
 *   }
 * )
 */
class SearchSuggester extends ConfigEntityBase implements SearchSuggesterInterface {

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
   * The search suggester heading.
   *
   * @var string
   */
  public $heading;

  /**
   * Whether or not this search suggester is enabled.
   *
   * @var bool
   */
  public $enabled;

  /**
   * The instantiated container.
   *
   * @var \Drupal\search_suggestions\Plugin\SearchSuggestions\Container\ContainerInterface
   */
  public $container;

  /**
   * The container id.
   *
   * @var string
   */
  public $container_id;

  /**
   * The container weight.
   *
   * @var integer
   */
  public $container_weight;

  /**
   * The instantiated display plugin.
   *
   * @var DisplayInterface
   */
  public $display;

  /**
   * The display plugin id.
   *
   * @var string
   */
  public $display_plugin_id;

  /**
   * The display plugin configuration.
   *
   * @var array
   */
  public $display_configuration;

  /**
   * The instantiated form type plugin.
   *
   * @var FormInterface
   */
  public $form;

  /**
   * The form type plugin id.
   *
   * @var string
   */
  public $form_plugin_id;

  /**
   * The form type plugin configuration.
   *
   * @var array
   */
  public $form_configuration;

  /**
   * The instantiated searcher plugin.
   *
   * @var SearcherInterface
   */
  public $searcher;

  /**
   * The searcher plugin id.
   *
   * @var string
   */
  public $searcher_plugin_id;

  /**
   * The searcher plugin configuration.
   *
   * @var array
   */
  public $searcher_configuration;

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
  public function getHeading()
  {
    return $this->heading;
  }

  /**
   * {@inheritdoc}
   */
  public function setHeading($heading)
  {
    $this->heading = $heading;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled() {
    return $this->enabled;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnabled($enabled) {
    $this->enabled = $enabled;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainer() {
    $id = $this->getContainerId();
    $container = NULL;

    if ($id) {
      $container = \Drupal::entityTypeManager()
        ->getStorage('search_suggestions_container')
        ->load($id);
    }

    return $container;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayPlugin()
  {
    return $this->getPlugin('display', 'plugin.manager.search_suggester.display');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormPlugin()
  {
    return $this->getPlugin('form', 'plugin.manager.search_suggester.form');
  }

  /**
   * {@inheritdoc}
   */
  public function getSearcherPlugin()
  {
    return $this->getPlugin('searcher', 'plugin.manager.search_suggester.searcher');
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
      $pluginId = $this->get($type . '_plugin_id');
      $pluginConfig = $this->get($type . '_configuration');

      if ($pluginId) {
        $this->{$type} = $manager->createInstance($pluginId, $pluginConfig);
        $this->{$type}->setContextValue('search_suggester', $this);
      }
    }

    return $this->{$type};
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerId() {
    return $this->container_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setContainerId($id) {
    $this->container_id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerWeight() {
    return $this->container_weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setContainerWeight($weight) {
    $this->container_weight = $weight;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayPluginId()
  {
    return $this->display_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setDisplayPluginId($id)
  {
    $this->display_plugin_id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayConfiguration()
  {
    return $this->display_configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setDisplayConfiguration(array $configuration)
  {
    $this->display_configuration = $configuration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormPluginId()
  {
    return $this->form_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setFormPluginId($id)
  {
    $this->form_plugin_id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormConfiguration()
  {
    return $this->form_configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setFormConfiguration(array $configuration)
  {
    $this->form_configuration = $configuration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearcherPluginId()
  {
    return $this->searcher_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setSearcherPluginId($id)
  {
    $this->searcher_plugin_id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSearcherConfiguration()
  {
    return $this->searcher_configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setSearcherConfiguration(array $configuration)
  {
    $this->searcher_configuration = $configuration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    $libraries = ['search_suggestions/search_suggestions'];

    foreach ($this->getFormPlugin()->getAttachedLibraries() as $library) {
      $libraries[] = $library;
    }

    foreach ($this->getSearcherPlugin()->getAttachedLibraries() as $library) {
      $libraries[] = $library;
    }

    foreach ($this->getDisplayPlugin()->getAttachedLibraries() as $library) {
      $libraries[] = $library;
    }

    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {
    $classes = [];
    $classes[] = 'search-suggester';
    $classes[] = 'search-suggester--' . $this->id();

    foreach ($this->getFormPlugin()->getClasses() as $class) {
      $classes[] = $class;
    }

    foreach ($this->getSearcherPlugin()->getClasses() as $class) {
      $classes[] = $class;
    }

    foreach ($this->getDisplayPlugin()->getClasses() as $class) {
      $classes[] = $class;
    }

    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  public function getInputClasses() {
    $classes = [];
    $classes[] = 'search-suggester-input';
    $classes[] = 'search-suggester-input--' . $this->id();

    foreach ($this->getFormPlugin()->getInputClasses() as $class) {
      $classes[] = $class;
    }

    foreach ($this->getSearcherPlugin()->getInputClasses() as $class) {
      $classes[] = $class;
    }

    foreach ($this->getDisplayPlugin()->getInputClasses() as $class) {
      $classes[] = $class;
    }

    return $classes;
  }

}
