<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Annotation\SearchSuggesterSearcher;
use Drupal\search_suggestions\Result\CountResult;
use Drupal\search_suggestions\Result\ViewResult;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Provides a 'Views' searcher plugin.
 *
 * @SearchSuggesterSearcher(
 *   id = "views",
 *   label = @Translation("Views"),
 *   description = @Translation("Searches using a view with arguments."),
 *   result_types = {
 *     "view",
 *     "count",
 *   },
 *   context = {
 *     "search_suggester" = @ContextDefinition("entity:search_suggester", label = @Translation("Search suggester"))
 *   }
 * )
 *
 */
class ViewsSearcher extends SearcherBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
      'view' => '',
      'display' => 'master',
      'input_method' => 'contextual_filter',
      'input_id' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form['view'] = [
      '#type' => 'select',
      '#title' => $this->t('View'),
      '#options' => $this->getViewOptions(),
      '#default_value' => $this->getValue('view', $this->configuration),
    ];

    $form['display'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View display'),
      '#default_value' => $this->getValue('display', $this->configuration),
    ];

    $form['input_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Input method'),
      '#options' => $this->getInputMethodOptions(),
      '#default_value' => $this->getValue('input_method', $this->configuration),
    ];

    $form['input_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Input ID'),
      '#description' => $this->t('The ID of the filter that the input will be applied to in the view.'),
      '#default_value' => $this->getValue('input_id', $this->configuration),
    ];

    return $form;
  }

  private function getViewOptions() {
    $entityIds = \Drupal::service('entity.query')
      ->get('view')
      ->condition('status', TRUE)
      ->execute();

    $options = [];
    foreach (\Drupal::entityTypeManager()->getStorage('view')->loadMultiple($entityIds) as $id => $view) {
      $options[$id] = $view->label();
    }

    asort($options);

    return $options;
  }

  private function getInputMethodOptions() {
    return [
      'contextual_filter' => $this->t('Contextual filter'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValue($this->formStateKey, []);
    $this->configuration['view'] = $this->getValue('view', $values);
    $this->configuration['display'] = $this->getValue('display', $values);
    $this->configuration['input_method'] = $this->getValue('input_method', $values);
    $this->configuration['input_id'] = $this->getValue('input_id', $values);
  }

  private function getValue($key, $values) {
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
  public function search($input, $resultType)
  {
    $result = NULL;
    $view = $this->prepareView($input);

    if ($resultType === 'view' || $resultType === 'default') {
      $result = new ViewResult($view);
    }

    if ($resultType === 'count') {
      $result = new CountResult($view->total_rows);
    }

    return $result;
  }

  /**
   * @return ViewExecutable
   */
  private function prepareView($input) {
    $view = Views::getView($this->configuration['view']);

    if (is_object($view)) {
      $view->setArguments($this->getViewArguments($view, $input));
      $view->setDisplay($this->configuration['display']);
      $view->preExecute();
      $view->execute();
    } else {
      $view = NULL;
    }

    return $view;
  }

  private function getViewArguments(ViewExecutable $view, $input) {
    $argumentId = (int) $this->configuration['input_id'];
    $args = [];

    if ($argumentId >= 0) {
      $argumentIndex = 0;

      while ($argumentIndex < $argumentId) {
        $args[$argumentIndex] = NULL;
      }

      $args[$argumentIndex] = $input;
    }

    return $args;
  }

}
