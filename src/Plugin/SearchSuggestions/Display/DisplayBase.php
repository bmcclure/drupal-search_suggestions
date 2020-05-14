<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Display;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContextAwarePluginBase;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;
use Drupal\search_suggestions\Result\ResultInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DisplayBase extends ContextAwarePluginBase implements DisplayInterface {

  /**
   * The key to access the configuration settings within the form state values.
   *
   * @var string
   */
  protected $formStateKey = 'display_configuration';

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
      'result_type' => 'default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
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
  public function getResultTypes()
  {
    return isset($this->pluginDefinition['result_types']) ? $this->pluginDefinition['result_types'] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $configuration = $this->getConfiguration();

    $form['result_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Result type'),
      '#description' => $this->t('Select a compatible result type to display. "Default" will use the standard result type associated with this display plugin.'),
      '#options' => $this->getResultTypeOptions($form_state),
      '#default_value' => $configuration['result_type'],
    ];

    return $form;
  }

  protected function getResultTypeOptions(FormStateInterface $form_state) {
    $options = ['default' => $this->t('Default')];
    $types = $this->getResultTypes();
    $matches = [];

    /** @var SearchSuggesterInterface $searchSuggester */
    $searchSuggester = $this->getContextValue('search_suggester');
    $id = $form_state->getValue('searcher_plugin_id');
    $plugin = NULL;
    if ($id) {
      $config = $form_state->getValue('searcher_configuration', []);
      /** @var \Drupal\search_suggestions\SearchSuggesterSearcherManager $manager */
      $manager = \Drupal::service('plugin.manager.search_suggester.searcher');
      $plugin = $manager->createInstance($id, $config);
    } elseif ($searchSuggester->getSearcherPlugin()) {
      $plugin = $searchSuggester->getSearcherPlugin();
    }

    if ($plugin) {
      $matchTypes = $plugin->getResultTypes();
      $matches = array_values(array_intersect($types, $matchTypes));
    }

    if (!empty($matches)) {
      /** @var \Drupal\search_suggestions\ResultTypeManager $resultTypeManager */
      $resultTypeManager = \Drupal::service('search_suggestions.result_type_manager');
      foreach ($matches as $id) {
        $type = $resultTypeManager->getResultType($id);

        if ($type) {
          $options[$id] = $type['title'];
        }
      }
    }

    return $options;
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
    $defaults = $this->defaultConfiguration();
    $this->configuration['result_type'] = isset($values['result_type']) ? $values['result_type'] : $defaults['result_type'];
  }

  public function validateResult(ResultInterface $result) {
    return \in_array($result->getResultType(), $this->getResultTypes(), TRUE);
  }

  protected function getResultsContainer() {
    /** @var SearchSuggesterInterface $searchSuggester */
    $searchSuggester = $this->getContextValue('search_suggester');

    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['search-suggester-results', 'search-suggester-results--' . $searchSuggester->id()],
        'data-search-suggester' => $searchSuggester->id(),
      ],
      'inner' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['search-suggester-results-inner']],
      ]
    ];


    $heading = $searchSuggester->getHeading();

    if ($heading) {
      $build['inner']['heading'] = [
        '#type' => 'html_tag',
        '#tag' => 'h5',
        '#attributes' => ['class' => ['search-suggester-results__heading']],
        '#value' => $heading,
      ];
    }

    $build['inner']['container'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['search-suggester-results__ajax-container']],
    ];

    return $build;
  }

  public function build(ResultInterface $result, SearcherInterface $searcher) {
    $build = [];

    if ($this->validateResult($result)) {
      $build = $this->buildResult($result, $searcher);
    }

    return $build;
  }

  public function container() {
    return $this->getResultsContainer();
  }

  abstract protected function buildResult(ResultInterface $result, SearcherInterface $searcher);

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    return [];
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

}
