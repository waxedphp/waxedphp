<?php
namespace Waxedphp\Waxedphp\Reactions;

abstract class AbstractReaction {
  /**
   * @var \Waxedphp\Waxedphp\php\Base $base
   */
  protected \Waxedphp\Waxedphp\Base $base;
  /**
   * @var ?string $picked
   */
  protected ?string $picked = null;
  /**
  * constructor
  *
  * @param \Waxedphp\Waxedphp\php\Base $base
  */
  function __construct(\Waxedphp\Waxedphp\Base $base) {
    $this->base = $base;
  }

  /**
  * getBase
  *
  * @return object
  */
  protected function getBase():object {
    return $this->base;
  }


  /**
  * pick
  *
  * @param ?string $id
  * @return object
  */
  function pick(?string $id):object {
    if (is_string($id)) {
      $this->picked = trim($id, '#');
    }
    return $this;
  }

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    return [];
  }

}
