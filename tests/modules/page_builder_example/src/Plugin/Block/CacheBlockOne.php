<?php

namespace Drupal\page_builder_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * @Block(
 *   id = "page_builder_example__one",
 *   admin_label = @Translation(""),
 *   category = @Translation("")
 * )
 */
class CacheBlockOne extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#cache' => [
        'tags' => ['example_one_two'],
      ],
      '#markup' => 'one',
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'url';

    return $contexts;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    $tags[] = 'example_one';

    return $tags;
  }

}
