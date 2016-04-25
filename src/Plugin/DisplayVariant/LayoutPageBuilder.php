<?php

namespace Drupal\page_builder\Plugin\DisplayVariant;

use Drupal\Core\Display\VariantBase;
use Drupal\Core\Display\VariantInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a decorator for any variant to render its output within a layout.
 *
 * @DisplayVariant(
 *   id = "page_builder_layout",
 *   admin_label = @Translation("Page builder layout"),
 *   no_ui = TRUE,
 * )
 *
 * @todo Should we use $this->configuration or not?
 */
class LayoutPageBuilder extends VariantBase implements VariantInterface, ContainerFactoryPluginInterface {

  /**
   * The wrapped variant.
   *
   * @var \Drupal\Core\Display\VariantInterface
   */
  protected $variant;

  /**
   * The wrapped layout manager.
   *
   * @var \Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface
   */
  protected $layoutManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LayoutPluginManagerInterface $layoutPluginManager, VariantInterface $variant = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->layoutManager = $layoutPluginManager;
    $this->variant = $variant;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.layout_plugin')
    );
  }

  /**
   * Sets the wrapped variant.
   *
   * @param \Drupal\Core\Display\VariantInterface $variant
   *   The variant.
   *
   * @return $this
   *
   * @todo Add this onto the constructor?
   */
  public function setVariant(VariantInterface $variant) {
    $this->variant = $variant;
    return $this;
  }

  /**
   * Sets the layout ID which should be used to render.
   *
   * @param string $layout
   *   The layout ID.
   *
   * @return $this
   */
  public function setLayoutId($layout) {
    $this->configuration['layout'] = $layout;
    return $this;
  }

  /**
   * Returns the used layout ID.
   *
   * @return string
   *   The layout ID.
   */
  protected function getLayoutId() {
    return $this->configuration['layout'];
  }

  /**
   * Gets the used layout instance.
   *
   * @return \Drupal\layout_plugin\Plugin\Layout\LayoutInterface
   *   The layout instance.
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
