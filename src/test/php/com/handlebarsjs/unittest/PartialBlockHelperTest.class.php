<?php namespace com\handlebarsjs\unittest;

use unittest\Test;

class PartialBlockHelperTest extends HelperTest {

  #[Test]
  public function existing_partial() {
    $this->templates->add('layout', 'My layout');
    $this->assertEquals('My layout', $this->evaluate('{{#> layout}}My content{{/layout}}', []));
  }

  #[Test]
  public function existing_partial_with_partial_block() {
    $this->templates->add('layout', 'My layout {{> @partial-block }}');
    $this->assertEquals('My layout My content', $this->evaluate('{{#> layout}}My content{{/layout}}', []));
  }

  #[Test]
  public function non_existant_partial_renders_default() {
    $this->assertEquals('My content', $this->evaluate('{{#> layout}}My content{{/layout}}', []));
  }

  #[Test]
  public function nested_partial_block() {
    $this->templates->add('layout', '{{#> inner}}Inner{{/inner}} {{> @partial-block }}');
    $this->assertEquals('Inner Outer', $this->evaluate('{{#> layout}}Outer{{/layout}}', []));
  }

  #[Test]
  public function default_can_reference_options() {
    $this->assertEquals('Title', $this->evaluate('{{#> layout title="Title"}}{{title}}{{/layout}}', []));
  }

  #[Test]
  public function block_can_reference_options() {
    $this->templates->add('layout', '{{title}}');
    $this->assertEquals('Title', $this->evaluate('{{#> layout title="Title"}}Default{{/layout}}', []));
  }

  #[Test]
  public function block_can_reference_options_hash() {
    $this->templates->add('layout', '{{title}} - {{name.en}}');
    $this->assertEquals('Home - Tool', $this->evaluate('{{#> layout title="Home" name=theme.name}}Default{{/layout}}', [
      'theme' => ['name' => ['en' => 'Tool']]
    ]));
  }

  #[Test]
  public function partial_inside_each() {
    $this->templates->add('list', '[{{#each .}}<item>{{> @partial-block}}</item>{{/each}}]');
    $this->assertEquals(
      '[<item>value = a</item><item>value = b</item><item>value = c</item>]',
      $this->evaluate('{{#> list value}}value = {{.}}{{/list}}', ['value' => ['a', 'b', 'c']])
    );
  }

  #[Test]
  public function layout() {
    $this->templates->add('includes/hero', '<div class="hero"><img src="{{hero-src}}" alt="{{hero-alt}}"/></div>');
    $this->templates->add('layout', trim('
      <html>
        <head><title>{{title}}</title>{{#> head}}{{/head}}</head>
        <body>
          {{#> hero}}(No hero){{/hero}}
        </body>
      </html>
    '));

    $this->assertEquals(trim('
      <html>
        <head><title>Home</title><link rel="stylesheet" href="style.css"></head>
        <body>
          <div class="hero"><img src="hero.jpg" alt="Hero 1 alt title"/></div>
        </body>
      </html>'),
      $this->evaluate(trim('
        {{#> layout title="Home"}}
          {{#*inline "head"}}<link rel="stylesheet" href="style.css">{{/inline}}
          {{#*inline "hero"}}{{> includes/hero hero-src="hero.jpg" hero-alt="Hero 1 alt title"}}{{/inline}}
        {{/layout}}'),
        []
      )
    );
  }
}