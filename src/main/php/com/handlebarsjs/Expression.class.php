<?php namespace com\handlebarsjs;

use util\Objects;

class Expression implements \lang\Value {
  protected $name;
  protected $options;

  /**
   * Creates a new lookup instance
   *
   * @param  string $name
   * @param  var[] $options
   */
  public function __construct($name, $options= []) {
    $this->name= $name;
    $this->options= $options;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return '('.$this->name.($this->options ? ' '.implode(' ', $this->options) : '').')';
  }

  /**
   * Invocation overloading
   *
   * @param  var $context
   * @return var
   */
  public function __invoke($context) {
    $r= $context->lookup($this->name);
    if ($context->isCallable($r)) {

      // Subexpressions are called with their options as arguments,
      // which in turn may be subexpressions or values to be looked up.
      $pass= [];
      foreach ($this->options as $key => $option) {
        $pass[$key]= $option($context);
      }
      return $r(null, $context, $pass);
    } else {
      return $r;
    }
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    if (!($value instanceof self)) return 1;
    if (0 !== ($c= strcmp($this->name, $value->name))) return $c;
    return Objects::compare($this->options, $value->options);
  }

  /** @return string */
  public function hashCode() {
    return md5($this->name.serialize($this->options));
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.$this.')';
  }
}