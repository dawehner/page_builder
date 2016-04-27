<?php

namespace Drupal\Tests\page_builder\Unit;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextsManager;
use Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant;
use Symfony\Component\DependencyInjection\Container;

/**
 * @coversDefaultClass \Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant
 * @group page_builder
 */
class PageBuilderVariantTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers ::appendRenderArray
   * @covers ::build
   */
  public function testAppendPage() {
    $page_builder = $this->setupPageBuilder();

    $page_builder->appendRenderArray('first', ['#markup' => 'giraffe1']);
    $page_builder->appendRenderArray('first', ['#markup' => 'giraffe2']);

    $result = $page_builder->build();
    $this->assertEquals('giraffe1', $result['first'][0]['#markup']);
    $this->assertEquals('giraffe2', $result['first'][1]['#markup']);
  }

  /**
   * @covers ::prependRenderArray
   * @covers ::build
   */
  public function testPrependRenderArray() {
    $page_builder = $this->setupPageBuilder();

    $page_builder->prependRenderArray('first', ['#markup' => 'giraffe1']);
    $page_builder->prependRenderArray('first', ['#markup' => 'giraffe2']);

    $result = $page_builder->build();
    $this->assertEquals('giraffe2', $result['first'][0]['#markup']);
    $this->assertEquals('giraffe1', $result['first'][1]['#markup']);
  }

  /**
   * @covers ::appendBlock
   * @covers ::build
   */
  public function testAppendBlock() {
    $block_plugin1 = $this->prophesize(BlockPluginInterface::class);
    $block_plugin1->build()->willReturn(['#markup' => 'giraffe1']);
    $block_plugin1->getConfiguration()->willReturn([]);
    $block_plugin1->getPluginId()->willReturn('first');
    $block_plugin1->getBaseId()->willReturn('first');
    $block_plugin1->getDerivativeId()->willReturn('');
    $block_plugin1->getCacheTags()->willReturn([]);
    $block_plugin1->getCacheContexts()->willReturn([]);
    $block_plugin1->getCacheMaxAge()->willReturn(0);

    $block_plugin2 = $this->prophesize(BlockPluginInterface::class);
    $block_plugin2->build()->willReturn(['#markup' => 'giraffe2']);
    $block_plugin2->getConfiguration()->willReturn([]);
    $block_plugin2->getPluginId()->willReturn('second');
    $block_plugin2->getBaseId()->willReturn('second');
    $block_plugin2->getDerivativeId()->willReturn('');
    $block_plugin2->getCacheTags()->willReturn([]);
    $block_plugin2->getCacheContexts()->willReturn([]);
    $block_plugin2->getCacheMaxAge()->willReturn(0);

    $block_plugin_manager = $this->prophesize(PluginManagerInterface::class);

    $block_plugin_manager->createInstance('first', [])->willReturn($block_plugin1->reveal());
    $block_plugin_manager->createInstance('second', [])->willReturn($block_plugin2->reveal());

    $page_builder = $this->setupPageBuilder($block_plugin_manager);
    $page_builder->appendBlock('first', 'first', []);
    $page_builder->appendBlock('first', 'second', []);
    $page_builder->appendBlock('second', 'first', []);
    $page_builder->appendBlock('second', 'second', []);

    $result = $page_builder->build();
    $this->assertArrayHasKey('first', $result);
    $this->assertArrayHasKey('second', $result);

    $this->assertCount(2, $result['first']);
    $this->assertCount(2, $result['second']);

    // Ensure the order.
    $this->assertEquals('first', $result['first'][0]['#plugin_id']);
    $this->assertEquals('first', $result['second'][0]['#plugin_id']);
    $this->assertEquals('second', $result['first'][1]['#plugin_id']);
    $this->assertEquals('second', $result['second'][1]['#plugin_id']);
  }

  /**
   * @covers ::prependBlock
   * @covers ::build
   */
  public function testPrependBlock() {
    $block_plugin1 = $this->prophesize(BlockPluginInterface::class);
    $block_plugin1->build()->willReturn(['#markup' => 'giraffe1']);
    $block_plugin1->getConfiguration()->willReturn([]);
    $block_plugin1->getPluginId()->willReturn('first');
    $block_plugin1->getBaseId()->willReturn('first');
    $block_plugin1->getDerivativeId()->willReturn('');
    $block_plugin1->getCacheTags()->willReturn([]);
    $block_plugin1->getCacheContexts()->willReturn([]);
    $block_plugin1->getCacheMaxAge()->willReturn(0);

    $block_plugin2 = $this->prophesize(BlockPluginInterface::class);
    $block_plugin2->build()->willReturn(['#markup' => 'giraffe2']);
    $block_plugin2->getConfiguration()->willReturn([]);
    $block_plugin2->getPluginId()->willReturn('second');
    $block_plugin2->getBaseId()->willReturn('second');
    $block_plugin2->getDerivativeId()->willReturn('');
    $block_plugin2->getCacheTags()->willReturn([]);
    $block_plugin2->getCacheContexts()->willReturn([]);
    $block_plugin2->getCacheMaxAge()->willReturn(0);

    $block_plugin_manager = $this->prophesize(PluginManagerInterface::class);

    $block_plugin_manager->createInstance('first', [])->willReturn($block_plugin1->reveal());
    $block_plugin_manager->createInstance('second', [])->willReturn($block_plugin2->reveal());

    $page_builder = $this->setupPageBuilder($block_plugin_manager);
    $page_builder->prependBlock('first', 'first', []);
    $page_builder->prependBlock('first', 'second', []);
    $page_builder->prependBlock('second', 'first', []);
    $page_builder->prependBlock('second', 'second', []);

    $result = $page_builder->build();
    $this->assertArrayHasKey('first', $result);
    $this->assertArrayHasKey('second', $result);

    $this->assertCount(2, $result['first']);
    $this->assertCount(2, $result['second']);

    // Ensure the order.
    $this->assertEquals('first', $result['first'][1]['#plugin_id']);
    $this->assertEquals('first', $result['second'][1]['#plugin_id']);
    $this->assertEquals('second', $result['first'][0]['#plugin_id']);
    $this->assertEquals('second', $result['second'][0]['#plugin_id']);
  }

  /**
   * @covers ::buildBlock
   */
  public function testCacheMetadataMergingInBuildBlock() {
    $contexts_manager = $this->prophesize(CacheContextsManager::class);
    $contexts_manager->assertValidTokens(['url', 'site'])->willReturn(TRUE);
    $container = new Container();
    $container->set('cache_contexts_manager', $contexts_manager->reveal());
    \Drupal::setContainer($container);

    $block_plugin1 = $this->prophesize(BlockPluginInterface::class);
    $block_plugin1->build()->willReturn([
      '#markup' => 'giraffe1',
      '#cache' => [
        'max-age' => 1000,
        'tags' => ['muh', 'meh'],
        'contexts' => ['site'],
      ],
    ]);

    $build = [
      '#cache' => [
        'max-age' => 2000,
        'tags' => ['mhhhh'],
        'contexts' => ['url'],
      ],
      '#block_plugin' => $block_plugin1->reveal(),
    ];
    $result = $this->setupPageBuilder()->buildBlock($build);
    $cacheable_metadata = CacheableMetadata::createFromRenderArray($result);

    $this->assertEquals(['site', 'url'], $cacheable_metadata->getCacheContexts());
    $this->assertEquals(['meh', 'mhhhh', 'muh'], $cacheable_metadata->getCacheTags());
    $this->assertEquals(1000, $cacheable_metadata->getCacheMaxAge());
  }

  /**
   * @return \Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant
   */
  protected function setupPageBuilder($block_plugin_manager = NULL) {
    $block_plugin_manager = $block_plugin_manager ?: $this->prophesize(PluginManagerInterface::class);
    return new PageBuilderVariant([], '', [], $block_plugin_manager->reveal());
  }

}
