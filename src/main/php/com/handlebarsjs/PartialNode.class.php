<?php namespace com\handlebarsjs;

use lang\Object;
use util\Objects;

/**
 * Partials
 *
 * @see  http://handlebarsjs.com/partials.html
 * @test xp://com.handlebarsjs.unittest.PartialNodeTest
 */
class PartialNode extends \com\github\mustache\Node {
  protected $template, $options, $indent;

  /**
   * Creates a new partial node
   *
   * @param lang.Object $template The template
   * @param [:var] $options
   * @param string $indent What to indent with
   */
  public function __construct(Object $template, $options= [], $indent= '') {
    $this->template= $template;
    $this->options= $options;
    $this->indent= $indent;
  }

  /**
   * Returns this partial's template
   *
   * @return lang.Object
   */
  public function template() { return $this->template; }

  /**
   * Returns options passed to this section
   *
   * @return string[]
   */
  public function options() { return $this->options; }

  /**
   * Returns options as string, indented with a space on the left if
   * non-empty, an empty string otherwise.
   *
   * @return string
   */
  protected function optionString() {
    $r= '';
    foreach ($this->options as $key => $option) {
      if (false !== strpos($option, ' ')) {
        $r.= ' '.$key.'= "'.$option.'"';
      } else {
        $r.= ' '.$key.'= '.$option;
      }
    }
    return $r;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'{{> '.$this->template->toString().$this->optionString().'}}, indent= "'.$this->indent.'"';
  }

  /**
   * Check whether a given value is equal to this node list
   *
   * @param  var $cmp The value
   * @return bool
   */
  public function equals($cmp) {
    return 
      $cmp instanceof self &&
      $this->indent === $cmp->indent &&
      Objects::equal($this->options, $cmp->options) &&
      $this->template->equals($cmp->template)
    ;
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {

    // {{> partial context}} vs {{> partial key="Value"}}
    if (isset($this->options[0])) {
      $context= $context->newInstance($this->options[0]($context));
    } else if ($this->options) {
      $context= $context->newInstance($context->asTraversable($this->options));
    }

    return $context->engine->transform($this->template->__invoke($context), $context, '{{', '}}', $this->indent);
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{> '.$this->template.$this->optionString().'}}';
  }
}