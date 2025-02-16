<?php
namespace JasterStary\Waxed\php\Setters\boilerplate;

use \JasterStary\Waxed\php\Setters\AbstractSetter;

class Setter extends AbstractSetter {

  /**
   * allowed options
   *
   * @var array<mixed> $_allowedOptions
   */
  protected array $_allowedOptions = [
  ];

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value): array {
    $a = [];
    $b = $this->getArrayOfAllowedOptions();
    if (!empty($b)) {
      $a['config'] = $b;
    }
    $a['value'] = $value;
    return $a;
  }

}
