<?php
namespace JasterStary\Waxed\php;

class Action {

  /**
   * @var array<mixed> $methods
   */
  private array $methods = [];
  /**
   * @var string $main
   */
  private ?string $main = null;
  /**
   * @var string $prefix
   */
  private string $prefix = '__';
  /**
   * @var Base $base
   */
  private Base $base;

  /**
  * constructor
  *
  * @param Base $base
  */
  public function __construct(Base $base){
    $this->base = &$base;
  }
  /**
  * get base
  *
  * @return object
  */
  public function getBase(): object {
    return $this->base;
  }
  /**
  * set prefix
  *
  * @param string $s
  * @return object
  */
  public function setPrefix(string $s): object {
    $this->prefix = $s;
    return $this;
  }

  /**
  * register
  *
  * @param object|array<object> $class
  * @param string $type
  * @return object
  */
  public function register(object|array $class, string $type = 'controller'): object {
    return $this->registerClass($class, $type);
  }

  /**
  * register class
  *
  * @param object|array<object> $cls
  * @param string $type
  * @param ?string $alias
  * @return object
  */
  public function registerClass(object|array $cls, string $type = 'controller', ?string $alias = null): object {
    if (is_array($cls)) {
      foreach ($cls as $alias => $cl) {
        $this->registerClass($cl, $type, $alias);
      };
      return $this;
    };
    //$c = get_class($cls);
    $reflect = new \ReflectionClass($cls);
    $c = $reflect->getShortName();
    if ((is_string($alias)) && (!is_numeric($alias))) {
      $c = $alias;
    };

    switch ($type) {
      case 'controller':
        $c = strtolower($c);
        if (!$this->main) {
          $this->main = $c;
        };
      break;
      case 'model':
        $c = preg_replace('/_model$/', '', $c);
        if ($c) $c = strtoupper($c);
      break;
    };
    $mm = get_class_methods($cls);
    //print_r($mm);
    foreach ($mm as $m) {
      if (preg_match('/^' . $this->prefix . '[a-zA-Z]([a-zA-Z0-9\_]+)$/', $m)) {
        if (is_callable([&$cls, $m])) {
          $this->methods[$c . '/' . preg_replace('/^' . $this->prefix . '/', '', $m)] = [&$cls, $m];
        }
      };
    };
    return $this;
  }

  /**
  * dispatch
  *
  * @param array<mixed> $data
  * @return mixed
  */
  function dispatch(array $data) {
    if (!isset($data['action'])) {
      return;
    };
    $action = explode('/', $data['action']);
    $actc = $this->main . '/default';
    $actb = $this->main . '/' . implode('_', $action);

    /* Too insecure. Deprecated.
    if (count($action) > 2) {
      $acta = $action[0] . '/' . $action[1];
    } else if (count($action) == 2) {
      $acta = $action[0] . '/' . $action[1];
    } else {
      $acta = $this->main . '/' . $action[0];
    }
    // do we have present method for structured call?
    if (isset($this->methods[$acta])) {
      $o = call_user_func($this->methods[$acta], $data);
      //print_r($o);
      //NOW WHAT?
      $this->base->display([
        'a' => $acta,
        'posted' => $data,
        'result' => $o,
      ], 'sss', false, "test")->flush();

      return $o;
    };
    */
    // do we have present base method for simple call?
    if (isset($this->methods[$actb])) {
      $o = call_user_func($this->methods[$actb], $data);
      return $o;
    };
    // do we have present some default method for unknown call?
    if (isset($this->methods[$actc])) {
      $o = call_user_func($this->methods[$actc], $data);
      return $o;
    };
  }

  /**
  * dispatch2
  *
  * @param array<mixed> $data
  * @return mixed
  */
  function dispatch2(string $action, ...$data) {
    $action = explode('/', $action);
    $actc = $this->main . '/default';
    $actb = $this->main . '/' . implode('_', $action);
    // do we have present base method for simple call?
    if (isset($this->methods[$actb])) {
      $o = call_user_func_array($this->methods[$actb], $data);
      return $o;
    };
    // do we have present some default method for unknown call?
    if (isset($this->methods[$actc])) {
      $o = call_user_func_array($this->methods[$actc], $data);
      return $o;
    };
  }

  /**
  * get list
  *
  * @return array<string>
  */
  function getList(): array {
    return array_keys($this->methods);
  }

}

