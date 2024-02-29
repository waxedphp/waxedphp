<?php
namespace Waxedphp\Waxedphp\Reactions;

class ScrollTop extends AbstractReaction {
  /**
   * @var ?int $onTime
   */
  private ?int $speed = null;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'scrollTop',
    );
    if($this->speed>0){
      $a['speed']=intval($this->speed);
    };
    return $a;
  }

  /**
  * pick
  *
  * @param ?string $id
  * @return object
  */
  function pick(?string $id):object {
    if ($id) {
      //$this->picked = $id;
    }
    return $this;
  }

  function configure(int $speed = 0): object {
    $this->speed = $speed;
    return $this;
  }

}

