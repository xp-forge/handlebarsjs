<?php namespace com\handlebarsjs\unittest;

use unittest\{Test, Values};

class WithHelperTest extends HelperTest {

  #[Test, Values([null, false, '', 0, [[]]])]
  public function does_not_show_for_falsy_values($value) {
    $this->assertEquals('', $this->evaluate('{{#with person}}-{{var}}-{{/with}}', [
      'var' => $value
    ]));
  }

  #[Test]
  public function switches_context() {
    $this->assertEquals('Alan Johnson', $this->evaluate(
      '{{#with person}}{{first}} {{last}}{{/with}}',
      ['person' => ['first' => 'Alan', 'last' => 'Johnson']]
    ));
  }

  #[Test, Values(['else', '^'])]
  public function else_invoked_for_non_truthy($else) {
    $this->assertEquals('Default', $this->evaluate('{{#with var}}-{{.}}-{{'.$else.'}}Default{{/with}}', [
      'var' => false
    ]));
  }
}