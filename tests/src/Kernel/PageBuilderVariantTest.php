<?php

namespace Drupal\Tests\page_builder\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant
 * @group page_builder
 */
class PageBuilderVariantTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'layout_plugin',
    'page_builder',
    'layout_plugin_example',
    'page_builder_example',
    'system',
    'user',
  ];

  public function testCacheability() {
    /** @var \Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant $page_builder_variant */
    $page_builder_variant = \Drupal::service('plugin.manager.display_variant')
      ->createInstance('page_builder');

    $page_builder_variant->appendBlock('top', 'page_builder_example__one');
    $page_builder_variant->appendBlock('top', 'page_builder_example__two');

    $original_build = $build = $page_builder_variant->build();

    $this->container->get('renderer')->renderRoot($build);
    $this->assertTrue(isset($build['top'][0]['#plugin_id']));
    $this->assertTrue(isset($build['top'][1]['#plugin_id']));
    $this->assertEquals($build['top'][0]['#markup'], 'one');
    $this->assertEquals($build['top'][1]['#markup'], 'two');
    $this->assertEquals([
      'example_one',
      'example_one_two',
    ], $build['top'][0]['#cache']['tags']);
    $this->assertEquals([
      'languages:language_interface',
      'theme',
      'url',
      'user.permissions',
    ],
      $build['top'][0]['#cache']['contexts']);
    $this->assertEquals([
      'example_two',
      'example_two_two',
    ], $build['top'][1]['#cache']['tags']);
    $this->assertEquals([
      'languages:language_interface',
      'theme',
      'url',
      'user.permissions',
    ],
      $build['top'][1]['#cache']['contexts']);
    $this->assertEquals([
      'example_one',
      'example_one_two',
      'example_two',
      'example_two_two',
    ], $build['#cache']['tags']);

    $build = $original_build;
    $this->container->get('renderer')->renderRoot($build);
    $this->assertFalse(isset($build['top'][0]['#plugin_id']));
    $this->assertFalse(isset($build['top'][1]['#plugin_id']));
    $this->assertEquals($build['top'][0]['#markup'], 'one');
    $this->assertEquals($build['top'][1]['#markup'], 'two');
    $this->assertEquals([
      'example_one',
      'example_one_two',
    ], $build['top'][0]['#cache']['tags']);
    $this->assertEquals([
      'languages:language_interface',
      'theme',
      'url',
      'user.permissions',
    ],
      $build['top'][0]['#cache']['contexts']);
    $this->assertEquals([
      'example_two',
      'example_two_two',
    ], $build['top'][1]['#cache']['tags']);
    $this->assertEquals([
      'languages:language_interface',
      'theme',
      'url',
      'user.permissions',
    ],
      $build['top'][1]['#cache']['contexts']);
    $this->assertEquals([
      'example_one',
      'example_one_two',
      'example_two',
      'example_two_two',
    ], $build['#cache']['tags']);
  }

}
