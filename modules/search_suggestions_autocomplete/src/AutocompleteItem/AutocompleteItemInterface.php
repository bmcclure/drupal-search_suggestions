<?php

namespace Drupal\search_suggestions_autocomplete\AutocompleteItem;

interface AutocompleteItemInterface {

  /**
   * Gets the full string that should be inserted into the search field when
   * this suggestion is selected.
   *
   * @return string|NULL
   *   The suggested input string, or NULL if the input should not be replaced.
   */
  public function getSuggestedInput();

  /**
   * Gets the URL that this suggestion should redirect to when selected.
   *
   * @return \Drupal\Core\Url|NULL
   *   The URL to redirect to, or NULL if the user should not be redirected.
   */
  public function getSuggestedUrl();

  /**
   * Gets the original user-supplied input that triggered this suggestion.
   *
   * @return string
   *   The original user input for this suggestion.
   */
  public function getOriginalInput();

  /**
   * Gets the suggested prefix that would go before the original input.
   *
   * @return string|NULL
   *   The suggested prefix, or NULL if there should not be a prefix added.
   */
  public function getSuggestedPrefix();

  /**
   * Gets the suggested suffix that would go after the original input.
   *
   * @return string|NULL
   *   The suggested suffix, or NULL if there should not be a suffix added.
   */
  public function getSuggestedSuffix();

  /**
   * Gets the number of search results that match this suggestion.
   *
   * @return int|NULL
   *   The number of matching results, or NULL if unknown or result counts don't
   *   make sense for this type of suggestion.
   */
  public function getResultCount();

}
