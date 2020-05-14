<?php

namespace Drupal\search_suggestions\Form;

/**
 * A basic search form.
 */
class BasicSearchForm extends SearchFormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'search_suggestions_search_form_basic';
  }
}
