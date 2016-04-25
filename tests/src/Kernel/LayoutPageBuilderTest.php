<?php

namespace Drupal\Tests\page_builder\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\page_builder\Plugin\DisplayVariant\LayoutPageBuilder
 * @group page_builder
 */
class LayoutPageBuilderTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'layout_plugin',
    'page_builder',
    'layout_plugin_example',
    'system',
  ];

  /**
   * @covers ::build
   */
  public function testLayout() {
    /** @var \Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant $page_builder_variant */
    $page_builder_variant = \Drupal::service('plugin.manager.display_variant')
      ->createInstance('page_builder');

    /** @var \Drupal\page_builder\Plugin\DisplayVariant\LayoutPageBuilder $layout_variant */
    $layout_variant = \Drupal::service('plugin.manager.display_variant')
      ->createInstance('page_builder_layout');

    $page_builder_variant->appendRenderArray('top', ['#markup' => 'first']);
    $page_builder_variant->appendRenderArray('top', ['#markup' => 'second']);
    $page_builder_variant->appendRenderArray('bottom', ['#markup' => 'third']);
    $page_builder_variant->appendRenderArray('bottom', ['#markup' => 'forth']);

    $result = $layout_variant
      ->setLayoutId('layout_example_1col')
      ->setVariant($page_builder_variant)
      ->build();

    $this->setRawContent($this->render($result));
    $top = $this->cssSelect('.region-top');
    $this->assertContains('first', $top[0]->asXML());
    $this->assertContains('second', $top[0]->asXML());

    $bottom = $this->cssSelect('.region-bottom');
    $this->assertContains('third', $bottom[0]->asXML());
    $this->assertContains('forth', $bottom[0]->asXML());
  }

}
