<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Annotation\SearchSuggesterForm;
use Drupal\search_suggestions\Entity\SearchSuggesterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides an 'Entity Renderer' display plugin.
 *
 * @SearchSuggesterForm(
 *   id = "form_api",
 *   label = @Translation("Form API"),
 *   description = @Translation("Attaches the suggester to a standard Form API form input."),
 *   context = {
 *     "search_suggester" = @ContextDefinition("entity:search_suggester", label = @Translation("Search suggester"))
 *   }
 * )
 *
 */
class FormApiForm extends FormBase {

  private static $applied = [];

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
      'forms' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
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
  public function applies(array &$form, FormStateInterface $formState, $formId)
  {
    $formConfig = $this->getFormConfig($form, $formId);
    return !is_null($formConfig);
  }

  /**
   * {@inheritdoc}
   */
  public function apply(array &$form, FormStateInterface $formState, $formId)
  {
    if (!$this->applies($form, $formState, $formId)) {
      return;
    }

    /** @var SearchSuggesterInterface $suggester */
    $suggester = $this->getContextValue('search_suggester');
    $formConfig = $this->getFormConfig($form, $formId);

    foreach ($suggester->getInputClasses() as $class) {
      $form[$formConfig['field_name']]['#attributes']['class'][] = $class;
    }
  }

  protected function getFormConfig(array &$form, $formId) {
    foreach ($this->configuration['forms'] as $formConfig) {
      if ($formConfig['form_id'] === $formId && isset($form[$formConfig['field_name']])) {
        return $formConfig;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getInputFromRequest(Request $request) {
    $input = '';

    foreach ($this->configuration['forms'] as $formConfig) {
      if ($request->request->has($formConfig['field_name'])) {
        $input = $request->request->get($formConfig['field_name'], '');
        break;
      } elseif ($request->query->has($formConfig['field_name'])) {
        $input = $request->query->get($formConfig['field_name'], '');
        break;
      }
    }

    return $input;
  }

  /**
   * {@inheritdoc}
   */
  public function getInputClasses() {
    $classes = parent::getInputClasses();
    $classes[] = 'search-suggester-input';
    return $classes;
  }

}
