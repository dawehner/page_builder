<?php

namespace Drupal\page_builder_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * @Block(
 *   id = "page_builder_example__two",
 *   admin_label = @Translation(""),
 *   category = @Translation("")
 * )
 */
class CacheBlockTwo extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#cache' => [
        'tags' => ['example_two_two'],
      ],
      '#markup' => 'two',
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
    $tags[] = 'example_two';

    return $tags;
  }

}
