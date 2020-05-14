<?php

namespace Drupal\search_suggestions\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Abstract base class for a search form.
 */
abstract class SearchFormBase extends FormBase {

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attributes']['class'][] = 'search-suggestions-search-form';

    $form['keys'] = [
      '#title' => $this->t('Search'),
      '#type' => 'textfield',
      '#default_value' => $this->getKeys($form_state),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'button',
      '#value' => $this->t('Search'),
      '#attributes' => ['class' => ['search-suggestions-submit']],
    ];
    return $form;
  }

  /**
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return string
   */
  protected function getKeys(FormStateInterface $form_state) {
    $value = '';

    if ($form_state->has('keys')) {
      $value = $form_state->get('keys');
    }

    $query = \Drupal::request()->query;

    if (!$value && $query && $query->has('keys')) {
      $value = $query->get('keys', '');
    }

    return $value;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Searching is handled via Javascript.
  }
}
