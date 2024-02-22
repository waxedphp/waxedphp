<?php
namespace Waxedphp\Waxedphp\php\Reactions;

class SendRaw extends AbstractReaction {
  /**
   * @var array<mixed> $parameters
   */
  private array $parameters = [];

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = $this->parameters;
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    return $a;
  }

  /**
  * configure
  *
  * @param array<mixed> $parameters
  * @return object
  */
  function configure(array $parameters):object {
    $this->parameters = $parameters;
    return $this;
  }

}

