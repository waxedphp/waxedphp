<?php
namespace Waxedphp\Waxedphp\Setters;

class Behaviors extends AbstractSetter {

  /**
  * pack behaviors
  *
  * @param array<string> $a
  * @return array<string>
  */
  private function packBehaviors(array $a) {
    $b = array();
    foreach ($a as $k => $v) {
      $b[$k] = '(function(){return function(o) {' . $v . '}})()';
    };
    return $b;
  }

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value): array {
    $a = $this->packBehaviors($value);
    return $a;
  }

}
