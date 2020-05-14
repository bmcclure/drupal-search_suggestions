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
 * Form handler for a search suggester add and edit forms.
 */
class SearchSuggesterForm extends EntityForm {

  /** @var \Drupal\Core\Entity\Query\QueryFactory */
  protected $entityQuery;

  /** @var  EntityFieldManager */
  protected $fieldManager;

  /**
   * Constructs a SearchSuggesterForm object.
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

    /** @var SearchSuggesterInterface $searchSuggester */
    $searchSuggester = $this->entity;

    $form['#prefix'] = '<div id="search-suggester-form-wrapper">';
    $form['#suffix'] = '</div>';
    $form['#tree'] = TRUE;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $searchSuggester->label(),
      '#description' => $this->t("Label for the search suggester."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $searchSuggester->id(),
      '#machine_name' => [
        'exists' => [$this, 'exists'],
      ],
      '#disabled' => !$searchSuggester->isNew(),
    ];

    $form['heading'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Heading'),
      '#description' => $this->t('An optional heading that can be displayed with the suggestions.'),
      '#default_value' => $searchSuggester->getHeading(),
    ];

    /** @var \Drupal\search_suggestions\SearchSuggestionsContainerManagerInterface $containerManager */
    $containerManager = \Drupal::service('search_suggestions_container.manager');
    $form['container_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Container'),
      '#description' => $this->t('Select the search suggestions container to display the suggester results in.'),
      '#options' => $containerManager->getContainerOptions('- Select -'),
      '#default_value' => $searchSuggester->getContainerId(),
    ];

    $weight = $searchSuggester->getContainerWeight();
    if (!$weight) {
      $weight = 0;
    }
    $form['container_weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Container weight'),
      '#description' => $this->t('Sets the display order of this suggester relative to others within the selected container.'),
      '#default_value' => $weight,
    ];

    $plugins = [
      'form' => [
        'title' => 'Form',
        'id' => $searchSuggester->getFormPluginId(),
        'config' => $searchSuggester->getFormConfiguration(),
      ],
      'searcher' => [
        'title' => 'Searcher',
        'id' => $searchSuggester->getSearcherPluginId(),
        'config' => $searchSuggester->getSearcherConfiguration(),
      ],
      'display' => [
        'title' => 'Display',
        'id' => $searchSuggester->getDisplayPluginId(),
        'config' => $searchSuggester->getDisplayConfiguration(),
      ]
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

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#description' => $this->t('When enabled, search suggesters will show up on the front end.'),
      '#default_value' => $searchSuggester->isEnabled(),
    ];

    return $form;
  }

  /**
   * Helper function to check whether a corresponding reference configuration entity exists.
   */
  public function exists($id) {
    $entity = $this->entityQuery->get('search_suggester')
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
        'wrapper' => 'search-suggester-form-wrapper',
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

      $manager = \Drupal::service('plugin.manager.search_suggester.' . $type);
      $plugin = $manager->createInstance($pluginId, $pluginConfig);
      $plugin->setContextValue('search_suggester', $this->entity);

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
    /** @var SearchSuggesterInterface $searchSuggester */
    $searchSuggester = $this->entity;
    $values = $form_state->getValues();

    if (!empty($values['display_configuration'])) {
      $searchSuggester->setDisplayPluginId($values['display_plugin_id']);
      $searchSuggester->setDisplayConfiguration($this->getPluginConfiguration('display', $form, $form_state));
    }

    if (!empty($values['form_configuration'])) {
      $searchSuggester->setFormPluginId($values['form_plugin_id']);
      $searchSuggester->setFormConfiguration($this->getPluginConfiguration('form', $form, $form_state));
    }

    if (!empty($values['searcher_configuration'])) {
      $searchSuggester->setSearcherPluginId($values['searcher_plugin_id']);
      $searchSuggester->setSearcherConfiguration($this->getPluginConfiguration('searcher', $form, $form_state));
    }

    $status = $searchSuggester->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label search suggester.', [
        '%label' => $searchSuggester->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label search suggester was not saved.', [
        '%label' => $searchSuggester->label(),
      ]));
    }

    $form_state->setRedirect('entity.search_suggester.collection');
  }

  private function getPluginConfiguration($pluginType, &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    /** @var SearchSuggesterInterface $searchSuggester */
    $searchSuggester = $this->entity;
    /** @var SearchSuggesterSearcherManager $manager */
    $manager = \Drupal::service('plugin.manager.search_suggester.' . $pluginType);
    /** @var SearcherInterface $plugin */
    $plugin = $manager->createInstance($values[$pluginType . '_plugin_id']);
    $plugin->setContextValue('search_suggester', $searchSuggester);
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

    /** @var SearchSuggesterInterface $entity */
    $entity->set('id', $values['id']);
    $entity->set('label', $values['label']);
    $entity->set('heading', $values['heading']);
    $entity->set('container_id', $values['container_id']);
    $entity->set('container_weight', $values['container_weight']);
    $entity->set('enabled', $values['enabled']);
  }

  /**
   * @param string $type
   *
   * @return array
   */
  private function getPluginOptions($type, $compatibilityCheckId = FALSE) {
    $manager = \Drupal::service('plugin.manager.search_suggester.' . $type);

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
