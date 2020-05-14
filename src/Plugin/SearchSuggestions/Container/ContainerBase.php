<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Container;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContextAwarePluginBase;

abstract class ContainerBase extends ContextAwarePluginBase implements ContainerInterface {

  /**
   * The key to access the configuration settings within the form state values.
   *
   * @var string
   */
  protected $formStateKey = 'container_configuration';

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configuration += $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration)
  {
    $this->configuration = $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
      'heading' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies()
  {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function label()
  {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription()
  {
    return isset($this->pluginDefinition['description']) ? $this->pluginDefinition['description'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getContainerType()
  {
    return isset($this->pluginDefinition['container_type']) ? $this->pluginDefinition['container_type'] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form['heading'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Heading'),
      '#description' => $this->t('An optional heading to show at the top of the container. Use "%s" to output the search keys within the heading.'),
      '#default_value' => $this->getValue('heading', $this->configuration),
    ];

    return $form;
  }

  /**
   * Gets a value from the provided values, falling back to the default
   * configuration.
   *
   * @param $key
   *   The key to look up.
   * @param $values
   *   The values to look for the key in.
   *
   * @return mixed
   *   The value (or the default value) for the provided key.
   */
  protected function getValue($key, $values) {
    $defaults = $this->defaultConfiguration();

    $value = '';

    if (isset($values[$key])) {
      $value = $values[$key];
    } elseif (isset($defaults[$key])) {
      $value = $defaults[$key];
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValue($this->formStateKey, []);
    $this->configuration['heading'] = $this->getValue('heading', $values);
  }

  /**
   * {@inheritdoc}
   */
  abstract public function build(array &$content);

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    return ['search_suggestions/search_suggestions_container'];
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getInputClasses() {
    return [];
  }

  protected function getResultsContainer() {
    $configuration = $this->getConfiguration();

    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => $this->getResultsContainerClasses(),
        //'style' => ['display: none;'], // @todo Get this working and remove from CSS
      ],
      'inner' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['search-suggestions-container-inner']],
      ]
    ];

    if ($configuration['heading']) {
      $build['inner']['heading'] = [
        '#type' => 'html_tag',
        '#tag' => 'h5',
        '#attributes' => [
          'class' => ['search-suggestions-container__heading'],
          'data-heading-template' => $configuration['heading'],
        ],
        '#value' => $configuration['heading'],
      ];
    }

    return $build;
  }

  protected function getResultsContainerClasses() {
    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $entity */
    $entity = $this->getContextValue('search_suggestions_container');

    return [
      'search-suggestions-container',
      'search-suggestions-container--type--' . $this->getContainerType(),
      'search-suggestions-container--plugin--' . $this->getPluginId(),
      'search-suggestions-container--id--' . $entity->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function formAlter(array &$form, FormStateInterface $form_state, $form_id) {
    // This method is optional.
  }

}
