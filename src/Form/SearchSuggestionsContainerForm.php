<?php

namespace Drupal\search_suggestions\Form;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;
use Drupal\search_suggestions\SearchSuggesterSearcherManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for a search suggestions container add and edit forms.
 */
class SearchSuggestionsContainerForm extends EntityForm {

  /** @var \Drupal\Core\Entity\Query\QueryFactory */
  protected $entityQuery;

  /** @var  EntityFieldManager */
  protected $fieldManager;

  /**
   * Constructs a SearchSuggestionsContainerForm object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   *
   * @param \Drupal\Core\Entity\EntityFieldManager $field_manager
   *   The entity field manager.
   */
  public function __construct(QueryFactory $entity_query, EntityFieldManager $field_manager) {
    $this->entityQuery = $entity_query;
    $this->fieldManager = $field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var QueryFactory $entity_query */
    $entity_query = $container->get('entity.query');

    /** @var EntityFieldManager $field_manager */
    $field_manager = $container->get('entity_field.manager');

    return new static(
      $entity_query,
      $field_manager
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $searchSuggestionsContainer */
    $searchSuggestionsContainer = $this->entity;

    $form['#prefix'] = '<div id="search-suggestions-container-form-wrapper">';
    $form['#suffix'] = '</div>';
    $form['#tree'] = TRUE;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $searchSuggestionsContainer->label(),
      '#description' => $this->t('Label for the search suggestions container.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $searchSuggestionsContainer->id(),
      '#machine_name' => [
        'exists' => [$this, 'exists'],
      ],
      '#disabled' => !$searchSuggestionsContainer->isNew(),
    ];

    $plugins = [
      'container' => [
        'title' => 'Container',
        'id' => $searchSuggestionsContainer->getContainerPluginId(),
        'config' => $searchSuggestionsContainer->getContainerConfiguration(),
      ],
    ];

    foreach ($plugins as $key => $config) {
      $compatibilityId = NULL;
      if ($key === 'display') {
        $compatibilityId = $this->getPluginId('searcher', $form_state->getUserInput(), $plugins['searcher']['id']);
      }
      $pluginId = $this->getPluginId($key, $form_state->getUserInput(), $config['id']);
      $pluginConfig = $this->getPluginConfig($key, $form_state->getUserInput(), $config['config']);
      $form[$key . '_plugin_id'] = $this->getPluginIdElement($key, $config['title'], $pluginId, $form_state, $compatibilityId);
      $form[$key . '_configuration'] = $this->getPluginConfigElement($pluginId, $key, $config['title'] . ' settings', $pluginConfig, $form_state);
    }

    return $form;
  }

  /**
   * Helper function to check whether a corresponding reference configuration entity exists.
   */
  public function exists($id) {
    $entity = $this->entityQuery->get('search_suggestions_container')
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

  public function pluginChangeCallback(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  private function getPluginId($type, $input, $default) {
    return !empty($input[$type . '_plugin_id']) ? $input[$type . '_plugin_id'] : $default;
  }

  private function getPluginIdElement($type, $title, $pluginId, FormStateInterface $formState, $compatibilityCheckId = NULL) {
    return [
      '#type' => 'select',
      '#title' => $this->t($title),
      '#options' => $this->getPluginOptions($type, $compatibilityCheckId),
      '#default_value' => $pluginId,
      '#ajax' => [
        'callback' => [$this, 'pluginChangeCallback'],
        'event' => 'change',
        'wrapper' => 'search-suggestions-container-form-wrapper',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Updating form...'),
        ],
      ],
    ];
  }

  private function getPluginConfig($type, $input, $default) {
    return !empty($input[$type . '_configuration']) ? $input[$type . '_configuration'] : $default;
  }

  private function getPluginConfigElement($pluginId, $type, $title, $pluginConfig, FormStateInterface $formState) {
    $element = [];

    if (!empty($pluginId)) {
      if (!is_array($pluginConfig)) {
        $pluginConfig = [];
      }

      $manager = \Drupal::service('plugin.manager.search_suggestions_container.' . $type);
      $plugin = $manager->createInstance($pluginId, $pluginConfig);
      $plugin->setContextValue('search_suggestions_container', $this->entity);

      $fieldset = [
        '#type' => 'fieldset',
        '#title' => $this->t($title),
      ];

      $element = $plugin->buildConfigurationForm($fieldset, $formState);
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $searchSuggestionsContainer */
    $searchSuggestionsContainer = $this->entity;

    $values = $form_state->getValues();

    $pluginTypes = ['container'];

    foreach ($pluginTypes as $pluginType) {
      if (!empty($values[$pluginType . '_configuration'])) {
        $searchSuggestionsContainer->setContainerPluginId($values[$pluginType . '_plugin_id']);
        $searchSuggestionsContainer->setContainerConfiguration($this->getPluginConfiguration($pluginType, $form, $form_state));
      }
    }

    $status = $searchSuggestionsContainer->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label search suggestions container.', [
        '%label' => $searchSuggestionsContainer->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label search suggestions container was not saved.', [
        '%label' => $searchSuggestionsContainer->label(),
      ]));
    }

    $form_state->setRedirect('entity.search_suggestions_container.collection');
  }

  private function getPluginConfiguration($pluginType, &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $searchSuggestionsContainer */
    $searchSuggestionsContainer = $this->entity;
    /** @var SearchSuggesterSearcherManager $manager */
    $manager = \Drupal::service('plugin.manager.search_suggestions_container.' . $pluginType);
    /** @var SearcherInterface $plugin */
    $plugin = $manager->createInstance($values[$pluginType . '_plugin_id']);
    $plugin->setContextValue('search_suggestions_container', $searchSuggestionsContainer);
    $plugin->submitConfigurationForm($form[$pluginType . '_configuration'], $form_state);
    return $plugin->getConfiguration();
  }

  /**
   * Copies form values into the config entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The config entity.
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($this->entity instanceof EntityWithPluginCollectionInterface) {
      // Do not manually update values represented by plugin collections.
      $values = array_diff_key($values, $this->entity->getPluginCollections());
    }

    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $entity */
    $entity->set('id', $values['id']);
    $entity->set('label', $values['label']);
  }

  /**
   * @param string $type
   *
   * @return array
   */
  private function getPluginOptions($type, $compatibilityCheckId = FALSE) {
    $manager = \Drupal::service('plugin.manager.search_suggestions_container.' . $type);

    if ($compatibilityCheckId !== FALSE && method_exists($manager, 'getCompatibleDefinitions')) {
      $plugins = $manager->getCompatibleDefinitions($compatibilityCheckId);
    } else {
      $plugins = $manager->getDefinitions();
    }

    $options = ['' => $this->t('- Select -')];

    foreach ($plugins as $id => $definition) {
      $options[$id] = $definition['label'];
    }

    return $options;
  }

}
