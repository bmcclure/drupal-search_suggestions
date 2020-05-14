<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Container;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

abstract class FormContainerBase extends ContainerBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    $defaults = parent::defaultConfiguration();
    $defaults['forms'] = [];
    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $forms = $this->configuration['forms'];
    $values = [];
    if (is_array($forms)) {
      foreach ($forms as $formConfig) {
        if (isset($formConfig['form_id']) && isset($formConfig['field_name'])) {
          $values[] = $formConfig['form_id'] . ':' . $formConfig['field_name'];
        }
      }
    }

    $form['forms'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Form IDs'),
      '#description' => $this->t('A list of form IDs and field names, one per line, in the format "form_id:field_name".'),
      '#default_value' => implode("\n", $values),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitConfigurationForm($form, $form_state);

    $values = $form_state->getValue($this->formStateKey, []);
    $forms = [];
    $formValues = preg_split('/\r\n|[\r\n]/', $values['forms']);
    foreach ($formValues as $formValue) {
      if (strpos($formValue, ':') !== FALSE) {
        list($formId, $fieldName) = explode(':', $formValue);
        $forms[] = ['form_id' => $formId, 'field_name' => $fieldName];
      }
    }

    $this->configuration['forms'] = $forms;
  }

  /**
   * {@inheritdoc}
   */
  public function formAlter(array &$form, FormStateInterface $form_state, $form_id) {
    foreach ($this->configuration['forms'] as $formConfig) {
      if ($form_id === $formConfig['form_id']) {
        $this->attachContainer($form, $formConfig);
        $form['#attributes']['class'][] = 'search-suggestions-container-form';
      }
    }
  }

  protected function attachContainer(&$form, $formConfig) {
    if (!empty($formConfig['field_name'])) {
      $this->attachContainerToFormField($form, $formConfig['field_name']);
    } else {
      $this->attachContainerToForm($form);
    }
  }

  protected function attachContainerToFormField(&$form, $fieldName) {
    $after = !empty($form[$fieldName]['#suffix']) ? $form[$fieldName]['#suffix'] : '';
    $after .= $this->renderFormElement();
    $form[$fieldName]['#suffix'] = $after;
  }

  protected function attachContainerToForm(&$form) {
    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $entity */
    $entity = $this->getContextValue('search_suggestions_container');

    $form[$entity->id() . '--' . $this->getPluginId()] = [
      '#type' => 'markup',
      '#markup' => Markup::create($this->renderFormElement()),
    ];
  }

  protected function renderFormElement() {
    $build = $this->buildFormElement();
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');
    return $renderer->render($build);
  }

  protected function buildFormElement() {
    /** @var \Drupal\search_suggestions\Entity\SearchSuggestionsContainerInterface $entity */
    $entity = $this->getContextValue('search_suggestions_container');
    return $entity->build();
  }

}
