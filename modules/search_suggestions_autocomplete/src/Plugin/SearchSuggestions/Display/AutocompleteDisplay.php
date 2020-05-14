<?php

namespace Drupal\search_suggestions_autocomplete\Plugin\SearchSuggestions\Display;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Annotation\SearchSuggesterDisplay;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Display\DisplayBase;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;
use Drupal\search_suggestions_autocomplete\Result\AutocompleteResult;
use Drupal\search_suggestions\Result\ResultInterface;
use Drupal\search_suggestions_autocomplete\AutocompleteBuilderManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Autocomplete' display plugin.
 *
 * @SearchSuggesterDisplay(
 *   id = "autocomplete",
 *   label = @Translation("Autocomplete"),
 *   description = @Translation("Displays a list of autocomplete results."),
 *   result_types = {
 *     "autocomplete",
 *   },
 *   context = {
 *     "search_suggester" = @ContextDefinition("entity:search_suggester", label = @Translation("Search suggester"))
 *   }
 * )
 *
 */
class AutocompleteDisplay extends DisplayBase {

  /** @var AutocompleteBuilderManager */
  protected $autocompleteBuilderManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, AutocompleteBuilderManager $autocompleteBuilderManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->autocompleteBuilderManager = $autocompleteBuilderManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('search_suggestions_autocomplete.builder_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    $defaults = parent::defaultConfiguration();

    return $defaults + [
      'builder' => 'default',
      'search_path' => '',
      'direct_search' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['builder'] = [
      '#type' => 'select',
      '#title' => $this->t('Autocomplete builder'),
      '#description' => $this->t('A builder is responsible for displaying the autocomplete output. Other modules can provide alternative builders.'),
      '#options' => $this->getBuilderOptions(),
      '#default_value' => $this->configuration['builder'],
    ];

    $form['search_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search path'),
      '#description' => $this->t('When sending users to a search results page, the search keys will be appended to the end of this path.'),
      '#default_value' => $this->configuration['search_path'],
    ];

    $form['direct_search'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send users directly to the search results page when choosing a suggestion.'),
      '#description' => $this->t('When checked, clicking a suggestion will redirect the user to the search page like a normal link instead of populating the search box.'),
      '#default_value' => $this->configuration['direct_search'],
    ];

    return $form;
  }

  private function getBuilderOptions() {
    $builders = $this->autocompleteBuilderManager->getAutocompleteBuilderTypes();
    $options = [];
    foreach ($builders as $id => $builder) {
      $options[$id] = $builder['title'];
    }
    asort($options);
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitConfigurationForm($form, $form_state);
    $values = $form_state->getValue($this->formStateKey, []);
    $defaults = $this->defaultConfiguration();
    $this->configuration['builder'] = isset($values['builder']) ? $values['builder'] : $defaults['builder'];
    $this->configuration['search_path'] = isset($values['search_path']) ? $values['search_path'] : $defaults['search_path'];
    $this->configuration['direct_search'] = isset($values['direct_search']) ? $values['direct_search'] : $defaults['direct_search'];
  }

  /**
   * {@inheritdoc}
   */
  protected function buildResult(ResultInterface $result, SearcherInterface $searcher) {
    $build = [];

    if ($result instanceof AutocompleteResult) {
      $build = $this->getBuilder()->buildList($result);
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedLibraries() {
    return $this->getBuilder()->getLibraries();
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {
    $classes = ['search-suggester-autocomplete'];

    $builder = $this->getBuilder();

    foreach ($builder->getClasses() as $class) {
      $classes[] = $class;
    }

    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  public function getInputClasses() {
    $classes = ['search-suggester-autocomplete-input'];

    $builder = $this->getBuilder();

    foreach ($builder->getInputClasses() as $class) {
      $classes[] = $class;
    }

    return $classes;
  }

  /**
   * @return \Drupal\search_suggestions_autocomplete\AutocompleteBuilder\AutocompleteBuilderInterface
   */
  private function getBuilder() {
    /** @var \Drupal\search_suggestions\Entity\SearchSuggesterInterface $suggester */
    $suggester = $this->getContextValue('search_suggester');
    return $this->autocompleteBuilderManager->getAutocompleteBuilder($this->configuration['builder'], $suggester);
  }

}
