<?php

namespace Drupal\search_suggestions\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\search_suggestions\SearchSuggestionsContainerStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for search suggestions containers.
 *
 * @see \Drupal\system\Plugin\Block\SystemMenuBlock
 */
class SearchSuggestionsContainerBlock extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var SearchSuggestionsContainerStorageInterface
   */
  protected $searchSuggestionsContainerStorage;

  /**
   * Constructs new SearchResultsContainerBlock.
   *
   * @param SearchSuggestionsContainerStorageInterface $searchSuggestionsContainerStorage
   *   The search results container storage.
   */
  public function __construct(SearchSuggestionsContainerStorageInterface $searchSuggestionsContainerStorage) {
    $this->searchSuggestionsContainerStorage = $searchSuggestionsContainerStorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')->getStorage('search_suggestions_container')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $containers = $this->searchSuggestionsContainerStorage->loadByType('block');
    foreach ($containers as $container => $entity) {
      $this->derivatives[$container] = $base_plugin_definition;
      $this->derivatives[$container]['admin_label'] = $entity->label();
      $this->derivatives[$container]['config_dependencies']['config'][] = $entity->getConfigDependencyName();
    }
    return $this->derivatives;
  }

}
