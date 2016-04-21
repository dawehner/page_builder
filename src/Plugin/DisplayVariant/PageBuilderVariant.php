<?php

namespace Drupal\page_builder\Plugin\DisplayVariant;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Display\VariantBase;
use Drupal\Core\Render\Element;

class PageBuilderVariant extends VariantBase {

  /**
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $blockPluginManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PluginManagerInterface $blockPluginManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->blockPluginManager = $blockPluginManager;
  }

  /**
   * {@inheritdoc}
   */
  protected $regions = [];

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->regions;
  }

  public function appendRenderArray($region, array $build) {
    $this->regions[$region][] = $build;
    return $this;
  }

  public function prependRenderArray($region, array $build) {
    array_unshift($this->regions[$region], $build);
    return $this;
  }

  public function appendBlock($region, $plugin_id, array $configuration = []) {
    return $this->appendRenderArray($region, $this->getBlockArray($plugin_id, $configuration));
  }

  public function prependBlock($region, $plugin_id, array $configuration = []) {
    return $this->prependRenderArray($region, $this->getBlockArray($plugin_id, $configuration));
  }

  /**
   * @param $plugin_id
   * @param array $configuration
   *
   * @return \Drupal\Core\Block\BlockPluginInterface
   */
  protected function getBlockInstance($plugin_id, array $configuration) {
    return $this->blockPluginManager->createInstance($plugin_id, $configuration);
  }

  protected function getBlockArray($plugin_id, array $configuration) {
    $block = $this->getBlockInstance($plugin_id, $configuration);
    $block_build = [
      '#theme' => 'block',
      '#attributes' => [],
      '#configuration' => $block->getConfiguration(),
      '#plugin_id' => $block->getPluginId(),
      '#base_plugin_id' => $block->getBaseId(),
      '#derivative_plugin_id' => $block->getDerivativeId(),
      '#block_plugin' => $block,
      '#pre_render' => [[$this, 'buildBlock']],
      // @todo add support for cacheing ...
//      '#cache' => [
//        'keys' => ['page_manager_block_display', $this->id(), 'block', $block_id],
//        // Each block needs cache tags of the page and the block plugin, as
//        // only the page is a config entity that will trigger cache tag
//        // invalidations in case of block configuration changes.
//        'tags' => Cache::mergeTags($this->getCacheTags(), $block->getCacheTags()),
//        'contexts' => $block->getCacheContexts(),
//        'max-age' => $block->getCacheMaxAge(),
//      ],
    ];
    return $block_build;
  }

  /**
   * #pre_render callback for building a block.
   *
   * Renders the content using the provided block plugin, if there is no
   * content, aborts rendering, and makes sure the block won't be rendered.
   */
  public function buildBlock($build) {
    $content = $build['#block_plugin']->build();
    // Remove the block plugin from the render array.
    unset($build['#block_plugin']);
    if ($content !== NULL && !Element::isEmpty($content)) {
      $build['content'] = $content;
    }
    else {
      // Abort rendering: render as the empty string and ensure this block is
      // render cached, so we can avoid the work of having to repeatedly
      // determine whether the block is empty. E.g. modifying or adding entities
      // could cause the block to no longer be empty.
      $build = [
        '#markup' => '',
        '#cache' => $build['#cache'],
      ];
    }
    // If $content is not empty, then it contains cacheability metadata, and
    // we must merge it with the existing cacheability metadata. This allows
    // blocks to be empty, yet still bubble cacheability metadata, to indicate
    // why they are empty.
    if (!empty($content)) {
      CacheableMetadata::createFromRenderArray($build)
        ->merge(CacheableMetadata::createFromRenderArray($content))
        ->applyTo($build);
    }
    return $build;
  }

}
