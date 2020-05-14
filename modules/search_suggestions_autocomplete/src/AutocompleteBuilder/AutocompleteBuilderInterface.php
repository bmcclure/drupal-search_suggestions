<?php

namespace Drupal\search_suggestions_autocomplete\AutocompleteBuilder;

use Drupal\search_suggestions_autocomplete\Result\AutocompleteResultInterface;
use Drupal\search_suggestions_autocomplete\AutocompleteItem\AutocompleteItemInterface;

interface AutocompleteBuilderInterface {

  /**
   * Returns a render array for the provided autocomplete item.
   *
   * @param AutocompleteItemInterface $item
   *   The autocomplete item to build a render array for.
   *
   * @return array|NULL
   *   The render array for the provided item, or NULL if the item can't or
   *   shouldn't be rendered for any reason.
   */
  public function buildItem(AutocompleteItemInterface $item);

  /**
   * Returns a render array for an entire set of autocomplete results.
   *
   * @param AutocompleteResultInterface $result
   *   The items to render.
   *
   * @return array|NULL
   *   The render array for the provided list of autocomplete items, or NULL if
   *   the list can't or shouldn't be rendered for any reason.
   */
  public function buildList(AutocompleteResultInterface $result);

  /**
   * If there is an AJAX callback required for this builder, it should
   * handle its response here.
   *
   * @param \Drupal\search_suggestions\Result\AutocompleteResultInterface $result
   *
   * @return array
   *   The data to return via AJAX.
   */
  public function buildAjaxResponse(AutocompleteResultInterface $result);

  /**
   * Gets an array of libraries that should be attached to the autocomplete
   * builder.
   *
   * This is meant to be overridden by builder plugins.
   *
   * @return array
   *   An array of library definitions to attach, or an empty array.
   */
  public function getLibraries();

  /**
   * Gets an array of classes to associate with the autocomplete suggester container.
   *
   * @return string[]
   *   An array of classes.
   */
  public function getClasses();

  /**
   * Gets an array of classes to associate with the autocomplete search input.
   *
   * @return string[]
   *   An array of classes.
   */
  public function getInputClasses();

}
