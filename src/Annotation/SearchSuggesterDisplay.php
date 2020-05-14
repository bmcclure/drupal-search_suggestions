<?php

namespace Drupal\search_suggestions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a search suggester display plugin.
 *
 * @Annotation
 */
class SearchSuggesterDisplay extends Plugin {

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

  /**
   * The type of results this display plugin supports.
   *
   * @var array
   */
  public $result_types;
}
