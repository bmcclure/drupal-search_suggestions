<?php

namespace Drupal\search_suggestions\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\search_suggestions\Form\BasicSearchForm;
use Drupal\search_suggestions\SearchSuggesterStorageInterface;
use Drupal\search_suggestions\SearchSuggestionsContainerStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an extended Menu block.
 *
 * @Block(
 *   id = "search_suggestions_container_block",
 *   admin_label = @Translation("Search suggestions container"),
 *   category = @Translation("Search suggestions"),
 *   deriver = "Drupal\search_suggestions\Plugin\Derivative\SearchSuggestionsContainerBlock"
 * )
 */
class SearchSuggestionsContainerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\search_suggestions\SearchSuggestionsContainerStorageInterface
   */
  protected $searchSuggestionsContainerStorage;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, SearchSuggestionsContainerStorageInterface $searchSuggestionsContainerStorage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->searchSuggestionsContainerStorage = $searchSuggestionsContainerStorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('search_suggestions_container')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $defaultConfig = parent::defaultConfiguration();
    $defaultConfig['search_form'] = '';
    return $defaultConfig;
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['search_form'] = [
      '#title' => $this->t('Search form'),
      '#description' => $this->t('Select the type of search form to show in this container.'),
      '#type' => 'select',
      '#options' => $this->getSearchFormOptions(),
      '#default_value' => $config['search_form'],
    ];
    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['search_form'] = $values['search_form'];
    parent::blockSubmit($form, $form_state);
  }

  protected function getSearchFormOptions() {
    return [
      '' => 'None',
      'basic' => $this->t('Basic search form'),
    ];
  }

  protected function getSearchFormClass() {
    $config = $this->getConfiguration();
    $classes = [
      'basic' => BasicSearchForm::class,
    ];

    $class = '';
    if (!empty($config['search_form']) && array_key_exists($config['search_form'], $classes)) {
      $class = $classes[$config['search_form']];
    }

    return $class;
  }

  protected function getSearchForm() {
    $class = $this->getSearchFormClass();

    $form = [];
    if (!empty($class)) {
      $form = \Drupal::formBuilder()->getForm($class);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $containerId = $this->getDerivativeId();
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['search-suggestions-container-block']],
    ];

    $searchForm = $this->getSearchForm();
    if (!empty($searchForm)) {
      $build['search_form'] = $searchForm;
    }

    if (!empty($containerId)) {
      /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $container */
      $container = $this->searchSuggestionsContainerStorage->load($containerId);
      $build['container'] = $container->build();
    }

    return $build;
  }

}
