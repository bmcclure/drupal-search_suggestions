<?php

namespace Drupal\search_suggestions_search_api\Plugin\SearchSuggestions\Searcher;

use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\SearchApiException;
use Drupal\search_api_autocomplete\AutocompleteBackendInterface;
use Drupal\search_api_autocomplete\Entity\Search;
use Drupal\search_api_autocomplete\SearchApiAutocompleteException;
use Drupal\search_suggestions\Annotation\SearchSuggesterSearcher;
use Drupal\search_suggestions\Plugin\SearchSuggestions\Searcher\SearcherBase;
use Drupal\search_suggestions_autocomplete\Result\AutocompleteResult;
use Drupal\search_suggestions\Result\CountResult;
use Drupal\search_suggestions\Result\EntityListResult;
use Drupal\search_suggestions_autocomplete\AutocompleteItem\AutocompleteItem;

/**
 * Provides a 'Search API' searcher plugin.
 *
 * @SearchSuggesterSearcher(
 *   id = "search_api",
 *   label = @Translation("Search API"),
 *   description = @Translation("Searches the specified Search API index."),
 *   result_types = {
 *     "entity_list",
 *     "count",
 *     "autocomplete"
 *   },
 *   context = {
 *     "search_suggester" = @ContextDefinition("entity:search_suggester", label = @Translation("Search suggester"))
 *   }
 * )
 *
 */
class SearchApiSearcher extends SearcherBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
      'search_index' => '',
      'result_limit' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $defaults = $this->defaultConfiguration();

    $form['search_index'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search index'),
      '#default_value' => isset($this->configuration['search_index']) ? $this->configuration['search_index'] : $defaults['search_index'],
    ];

    $form['result_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Result limit'),
      '#description' => $this->t('The maximum number of items to return, or 0 for unlimited.'),
      '#default_value' => isset($this->configuration['result_limit']) ? $this->configuration['result_limit'] : $defaults['result_limit'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValue($this->formStateKey, []);
    $defaults = $this->defaultConfiguration();
    $this->configuration['search_index'] = isset($values['search_index']) ? $values['search_index'] : $defaults['search_index'];
    $this->configuration['result_limit'] = isset($values['result_limit']) ? $values['result_limit'] : $defaults['result_limit'];
  }

  /**
   * {@inheritdoc}
   */
  public function search($input, $resultType = 'default')
  {
    $result = NULL;

    if ($resultType === 'entity' || $resultType === 'default') {
      $items = $this->getQuery($input, $this->configuration['result_limit'])
        ->execute()
        ->getResultItems();

      $entities = [];
      foreach ($items as $item) {
        /** @var EntityAdapter $adapter */
        $adapter = $item->getOriginalObject(TRUE);
        $entities[] = $adapter->getValue();
      }

      $result = new EntityListResult($entities);
    }

    if ($resultType === 'count') {
      $count = $this->getQuery($input, $this->configuration['result_limit'])
        ->execute()
        ->getResultCount();

      $result = new CountResult($count);
    }

    if ($resultType === 'autocomplete') {
      $backend = $this->getBackend();
      if ($backend && method_exists($backend, 'getAutocompleteSuggestions')) {
        $result = new AutocompleteResult($this->getAutocompleteItems($input));
      }
    }

    return $result;
  }

  private function getAutocompleteItems($keys) {
    // @todo Decide how configurable this should be
    $keys = trim($keys);
    $matches = [];
    $transliterator = \Drupal::service('transliteration');
    /** @var \Drupal\search_api_autocomplete\Utility\AutocompleteHelperInterface $autocompleteHelper */
    $autocompleteHelper = \Drupal::service('search_api_autocomplete.helper');
    $search = $this->getSearchEntity();

    if (!$search->hasValidIndex()) {
      return $matches;
    }

    try {
      // If the "Transliteration" processor is enabled for the search index, we
      // also need to transliterate the user input for autocompletion.
      if ($search->getIndex()->isValidProcessor('transliteration')) {
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $keys = $transliterator->transliterate($keys, $langcode);
      }
      $split_keys = $autocompleteHelper->splitKeys($keys);
      [, $incomplete] = $split_keys;
      $fulltextFields = $search->getIndex()->getFulltextFields();
      $query = $this->getQuery($keys, $this->configuration['result_limit'], $fulltextFields);
      if (!$query) {
        return $matches;
      }

      // @todo Add suggester ID to the end of the search ID.
      $query->setSearchId('search_suggestions_autocomplete');
      $query->addTag('search_suggestions_autocomplete');
      $query->preExecute();

      /** @var \Drupal\search_api_autocomplete\Suggestion\SuggestionInterface[] $suggestions */
      $suggestions = [];
      $backend = $this->getBackend();
      if ($backend && method_exists($backend, 'getAutocompleteSuggestions')) {
        $suggestions = $backend->getAutocompleteSuggestions($query, $search, $incomplete, $keys);
      }

      /**
       * @var int $index
       * @var \Drupal\search_api_autocomplete\Suggestion\SuggestionInterface $backendResult
       */
      foreach ($suggestions as $index => $backendResult) {
        $matches[] = new AutocompleteItem(
          $backendResult->getUserInput(),
          $backendResult->getSuggestedKeys(),
          $backendResult->getUrl(),
          $backendResult->getSuggestionPrefix(),
          $backendResult->getSuggestionSuffix(),
          $backendResult->getResultsCount()
        );
      }
    }
    catch (SearchApiAutocompleteException $e) {
      watchdog_exception('search_suggestions_autocomplete', $e, '%type while retrieving autocomplete suggestions: !message in %function (line %line of %file).');
    }
    catch (SearchApiException $e) {
      watchdog_exception('search_suggestions_autocomplete', $e, '%type while retrieving autocomplete suggestions: !message in %function (line %line of %file).');
    }

    return $matches;
  }

  private function getSearchEntity() {
    $values = [
      'id' => 'search_suggestions',
      'label' => 'Search suggestions',
      'index_id' => $this->configuration['search_index'],
      'suggester_settings' => [],
      'suggester_weights' => [],
      'suggester_limits' => [],
      'search_settings' => ['search_suggestions' => []],
    ];

    return new Search($values, 'search_api_autocomplete_search');
  }

  /**
   * @param $input
   *
   * @param bool $range
   *
   * @return \Drupal\search_api\Query\QueryInterface
   */
  private function getQuery($input, $range = FALSE, $fulltextFields = []) {
    $index = Index::load($this->configuration['search_index']);
    $currentLanguage = \Drupal::languageManager()->getCurrentLanguage();

    $query = $index->query()
      ->keys($input)
      ->addCondition('status', 1)
      ->setLanguages([$currentLanguage->getId()]);

    if ($range) {
      $query->range(0, $range);
    }

    if (!empty($fulltextFields)) {
      $query->setFulltextFields($fulltextFields);
    }


    return $query;
  }

  private function getBackend() {
    $index = Index::load($this->configuration['search_index']);

    if (!$index->hasValidServer()) {
      return NULL;
    }

    /** @var \Drupal\search_api\ServerInterface $server */
    $server = $index->getServerInstance();
    $backend = $server->getBackend();

    if (!$backend instanceof AutocompleteBackendInterface && !$server->supportsFeature('search_api_autocomplete')) {
      return NULL;
    }

    return $backend;
  }

}
