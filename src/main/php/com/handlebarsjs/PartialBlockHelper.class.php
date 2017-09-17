<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;

/**
 * Partial blocks
 *
 * @see   http://handlebarsjs.com/partials.html
 * @test  xp://com.handlebarsjs.unittest.PartialBlockHelperTest
 */
class PartialBlockHelper extends BlockNode {

  /**
   * Creates a new with block helper
   *
   * @param string[] $options
   * @param com.github.mustache.NodeList $fn
   * @param com.github.mustache.NodeList $inverse
   * @param string $start
   * @param string $end
   */
  public function __construct($options= [], NodeList $fn= null, NodeList $inverse= null, $start= '{{', $end= '}}') {
    $template= (string)array_shift($options);
    parent::__construct($template, $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $templates= $context->engine->getTemplates();

    $source= $templates->source($this->name);
    if ($source->exists()) {
      $previous= $templates->register('@partial-block', $this->fn);
      try {
        return $context->engine->render($source, $context, $this->start, $this->end, '');
      } finally {
        $templates->register('@partial-block', $previous);
      }
    } else {
      return $this->fn->evaluate($context);
    }
  }
}