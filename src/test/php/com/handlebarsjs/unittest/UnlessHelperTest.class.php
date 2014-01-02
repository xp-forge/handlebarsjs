<?php namespace com\handlebarsjs\unittest;

class UnlessHelperTest extends HelperTest {

  #[@test, @values([null, false, '', 0, [[]]])]
  public function shows_for_falsy_values($value) {
    $this->assertEquals('-Default-', $this->evaluate('{{#unless var}}-Default-{{/unless}}', array(
      'var' => $value
    )));
  }

  #[@test, @values([true, 'true', 1, 1.0, [['non-empty-array']] ])]
  public function does_not_show_for_truthy_values($value) {
    $this->assertEquals('', $this->evaluate('{{#unless var}}-Default-{{/unless}}', array(
      'var' => $value
    )));
  }
}