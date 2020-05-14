<?php

namespace Drupal\search_suggestions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a search suggester container plugin.
 *
 * @Annotation
 */
class SearchSuggestionsContainer extends Plugin {

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
   * Gets the type of this container (ex: "block" or "form")
   *
   * @var bool
   */
  public $type;

}
