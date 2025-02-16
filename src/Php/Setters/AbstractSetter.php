<?php
namespace Waxedphp\Waxedphp\Php\Setters;

class AbstractSetter {

  /**
   * @var \JasterStary\Waxed\Php\Base $base
   */
  protected \Waxedphp\Waxedphp\Php\Base $base;

  /**
   * allowed options
   *
   * @var array<mixed> $_allowedOptions
   */
  protected array $_allowedOptions = [];

  /**
  * constructor
  *
  * @param \JasterStary\Waxed\Php\Base $base
  */
  public function __construct(\Waxedphp\Waxedphp\Php\Base $base){
    $this->base = &$base;
  }

  /**
  * _traverse
  *
  * @param array<mixed> $data
  * @param string $dd
  * @return ?array<mixed>
  */
  public static function _traverse(array $data, string $dd): ?array {
    if(!$dd)return $data;
    $dd = explode('/', $dd);
    if (is_array($data)) {
      foreach ($dd as $d) {
        if (isset($data[$d])) {
          $data = $data[$d];
        } else {
          return null;
        };
      };
    };
    return $data;
  }

  /**
  * set allowed options, magic method
  *
  * @param string $name
  * @param array<mixed> $arguments
  * @return object
  */
  public function __call(string $name, array $arguments): object {
    if (strpos($name, 'set')===0) {
      $var = lcfirst(substr($name, 3));
      if (in_array($var, $this->_allowedOptions)) {
        $rp = new \ReflectionProperty($this, $var);
        $type = $rp->getType()->getName();
        $valueType = strtolower(gettype($arguments[0]));
        if ($valueType == 'boolean') $valueType = 'bool';
        if ($valueType == 'integer') $valueType = 'int';

        if ($type === 'union') {
          $propTypes = array_map(function($type) {
              return $type->getName();
          }, $rp->getType()->getTypes());
        } else {
          $propTypes = [$type];
        }
        //print_r([$name, $valueType, $propTypes]);
        if (in_array($valueType, $propTypes)) {
          $this->$var = $arguments[0];
        }
      }
    }
    return $this;
  }

  /**
  * grab allowed options
  *
  * @return array<mixed>
  */
  protected function getArrayOfAllowedOptions(): array {
    $b = array();
    foreach ($this->_allowedOptions as $option) {
      if ((isset($this->$option))&&(!is_null($this->$option))) {
        $b[$option] = $this->$option;
      }
    };
    return $b;
  }

  /**
  * value
  *
  * @return mixed
  */
  public function value(mixed $value): mixed {
    $a = $this->getArrayOfAllowedOptions();
    $a['value'] = $value;
    return $a;
  }

  /**
  * func
  *
  * @return callable
  */
  public function func(): callable {
    return function($value) {
      return $this->value($value);
    };
  }

}

