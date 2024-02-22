<?php
namespace JasterStary\Waxed\php\Reactions;

abstract class AbstractReaction {
  /**
   * @var \JasterStary\Waxed\php\Base $base
   */
  protected \JasterStary\Waxed\php\Base $base;
  /**
   * @var ?string $picked
   */
  protected ?string $picked = null;
  /**
  * constructor
  *
  * @param \JasterStary\Waxed\php\Base $base
  */
  function __construct(\JasterStary\Waxed\php\Base $base) {
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
