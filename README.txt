# Page builder
This module is about experimenting with providing a nice DX for applying blocks
and render arrays to layouts. This module is not meant to provide a panels like
interface or configuration, but by construction is just an API.

## Installation
The module is installed as every other module.

## Usage

* If you want to build a page use the ```\Drupal\page_builder\Plugin\DisplayVariant\PageBuilderVariant```
class, see ```\Drupal\page_builder_example\Controller\ExamplePageBuilderController```
as example.
* If you want to build a page with a specific layout leverage
```\Drupal\page_builder\Plugin\DisplayVariant\LayoutPageBuilder``` on top of it,
see ```\Drupal\page_builder_example\Controller\ExamplePageBuilderController``` again
for an example.
