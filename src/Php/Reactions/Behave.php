<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Behave extends AbstractReaction{
  /**
   * @var array<mixed> $actions
   */
  private array $actions = [];

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'behave',
      'actions' => $this->actions,
    );
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    return $a;
  }

  /**
  * configure
  *
  * @param array<mixed> $actions
  * @return object
  */
  function configure(array $actions):object {
    $this->actions=$actions;
    return $this;
  }

}
