<?php

namespace Drupal\search_suggestions\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a search suggester searcher plugin.
 *
 * @Annotation
 */
class SearchSuggesterSearcher extends Plugin {

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
   * The possible types of results this searcher can return
   *
   * @var string
   */
  public $result_types;
}
