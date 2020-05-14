<?php

namespace Drupal\search_suggestions_autocomplete\AutocompleteItem;

class AutocompleteItem implements AutocompleteItemInterface {

  private $originalInput;
  private $suggestedInput;
  private $suggestedUrl;
  private $suggestedPrefix;
  private $suggestedSuffix;
  private $resultCount;

  public function __construct($originalInput = NULL, $suggestedInput = NULL, $suggestedUrl = NULL, $suggestedPrefix = NULL, $suggestedSuffix = NULL, $resultCount = NULL) {
    $this->suggestedInput = $suggestedInput;
    $this->suggestedUrl = $suggestedUrl;
    $this->originalInput = $originalInput;
    $this->suggestedPrefix = $suggestedPrefix;
    $this->suggestedSuffix = $suggestedSuffix;
    $this->resultCount = $resultCount;
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalInput() {
    return $this->originalInput;
  }

  /**
   * {@inheritdoc}
   */
  public function getSuggestedInput() {
    return $this->suggestedInput;
  }

  /**
   * {@inheritdoc}
   */
  public function getSuggestedUrl() {
    return $this->suggestedUrl;
  }

  /**
   * {@inheritdoc}
   */
  public function getSuggestedPrefix() {
    return $this->suggestedPrefix;
  }

  /**
   * {@inheritdoc}
   */
  public function getSuggestedSuffix() {
    return $this->suggestedSuffix;
  }

  /**
   * {@inheritdoc}
   */
  public function getResultCount() {
    return $this->resultCount;
  }

}
