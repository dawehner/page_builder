<?php

namespace Drupal\page_builder\Plugin\DisplayVariant;

use Drupal\Core\Display\VariantBase;
use Drupal\Core\Display\VariantInterface;
use Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface;

class LayoutPageBuilder extends VariantBase implements VariantInterface {

  /**
   * @var \Drupal\Core\Display\VariantInterface
   */
  protected $variant;

  /**
   * @var \Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface
   */
  protected $layoutManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LayoutPluginManagerInterface $layoutPluginManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->layoutManager = $layoutPluginManager;
  }

  public function setVariant(VariantInterface $variant) {
    $this->variant = $variant;
  }

  public function setLayoutId($layout) {
    $this->configuration['layout'] = $layout;
    return $this;
  }

  protected function getLayoutId() {
    return $this->configuration['layout'];
  }

  /**
   * @return \Drupal\layout_plugin\Plugin\Layout\LayoutInterface
   */
  protected function getLayout() {
    return $this->layoutManager->createInstance($this->getLayoutId());
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $variant_build = $this->variant->build();

    return $this->getLayout()->build($variant_build);
  }

}
