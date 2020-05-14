<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Display;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Annotation\SearchSuggesterDisplay;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;
use Drupal\search_suggestions\Result\EntityListResult;
use Drupal\search_suggestions\Result\ResultInterface;

/**
 * Provides an 'Entity Renderer' display plugin.
 *
 * @SearchSuggesterDisplay(
 *   id = "entity_renderer",
 *   label = @Translation("Entity renderer"),
 *   description = @Translation("Renders a list of entities using a supplied view mode."),
 *   result_types = {
 *     "entity_list",
 *   },
 *   context = {
 *     "search_suggester" = @ContextDefinition("entity:search_suggester", label
 *   = @Translation("Search suggester"))
 *   }
 * )
 *
 */
class EntityRendererDisplay extends DisplayBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    $defaults = parent::defaultConfiguration();

    return $defaults + [
      'view_mode' => 'teaser',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('View mode'),
      '#description' => $this->t('Select the view mode to render the entities with.'),
      '#options' => $this->getViewModeOptions(),
      '#default_value' => $this->configuration['view_mode'],
    ];

    return $form;
  }

  private function getViewModeOptions() {
    $viewModes = \Drupal::entityQuery('entity_view_mode')->execute();
    $options = [];
    foreach ($viewModes as $viewMode) {
      list($entityType, $viewMode) = explode('.', $viewMode);
      $options[$viewMode] = $viewMode;
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
    $this->configuration['view_mode'] = isset($values['view_mode']) ? $values['view_mode'] : $defaults['view_mode'];
  }

  /**
   * {@inheritdoc}
   */
  protected function buildResult(ResultInterface $result, SearcherInterface $searcher) {
    $build = [];

    if ($result instanceof EntityListResult) {
      /** @var EntityInterface $entity */
      foreach ($result as $entity) {
        $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId());
        $entityView = $viewBuilder->view($entity, $this->configuration['view_mode']);
        $entityView['#attributes']['class'][] = 'search-suggester-result';
        $build[] = $entityView;
      }
    }

    return $build;
  }

}
