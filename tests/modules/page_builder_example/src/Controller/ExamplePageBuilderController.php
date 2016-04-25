<?php

namespace Drupal\page_builder_example\Controller;

use Drupal\page_builder\Plugin\DisplayVariant\LayoutPageBuilder;
use Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant;

class ExamplePageBuilderController {

  public function home() {
    $page_builder = new PageBuilderVariant([], '', [], \Drupal::service('plugin.manager.block'));

    $page_builder->appendRenderArray('main', ['#markup' => 'muh']);
    $page_builder->appendRenderArray('main', ['#markup' => 'meh']);
    $page_builder->appendRenderArray('main_two', ['#markup' => 'buuuh!']);

    $page_builder->appendBlock('main_two', 'views_block:news-block_1');

    $layout = new LayoutPageBuilder([], '', [], \Drupal::service('plugin.manager.layout_plugin'));
    $layout->setLayoutId('home');
    $layout->setVariant($page_builder);

    return $layout->build();
  }

}
