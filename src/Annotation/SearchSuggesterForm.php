<?php

namespace Drupal\search_suggestions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a search suggester form type plugin.
 *
 * @Annotation
 */
class SearchSuggesterForm extends Plugin {

  /**
   * The plugin label.
   *
   * @var string
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The plugin description.
   *
   * @var string
   *
   * @ingroup plugin_translatable
   */
  public $description;

}
