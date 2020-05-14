<?php

namespace Drupal\search_suggestions\Plugin\SearchSuggestions\Display;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_suggestions\Annotation\SearchSuggesterDisplay;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherInterface;
use Drupal\search_suggestions\Result\ResultInterface;
use Drupal\search_suggestions\Result\ViewResult;
use Drupal\views\ViewExecutable;

/**
 * Provides an 'Views' display plugin.
 *
 * @SearchSuggesterDisplay(
 *   id = "views",
 *   label = @Translation("Views"),
 *   description = @Translation("Displays the results of the provided Views display."),
 *   result_types = {
 *     "view",
 *   },
 *   context = {
 *     "search_suggester" = @ContextDefinition("entity:search_suggester", label = @Translation("Search suggester"))
 *   }
 * )
 *
 */
class ViewsDisplay extends DisplayBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    $defaults = parent::defaultConfiguration();

    return $defaults + [
      'display' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['display'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View display'),
      '#default_value' => $this->getValue('display', $this->configuration),
    ];

    return $form;
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
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitConfigurationForm($form, $form_state);

    $values = $form_state->getValue($this->formStateKey, []);
    $this->configuration['display'] = $this->getValue('display', $values);
  }

  /**
   * {@inheritdoc}
   */
  protected function buildResult(ResultInterface $result, SearcherInterface $searcher) {
    $build = [];

    if ($result instanceof ViewResult) {
      /** @var ViewExecutable $view */
      $view = $result->getResult();
      // @todo Decide which one of these is best
      //$build = $view->buildRenderable($this->configuration['display'], $view->args);
      $build[] = $view->render($this->configuration['display']);
    }

    return $build;
  }
}
