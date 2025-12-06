<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class ScrollTo extends AbstractReaction {
  /**
   * @var ?int $onTime
   */
  private ?int $speed = null;
  
  private ?string $name = null;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'scrollTo',
      'name' => $this->name
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

  function configure(string $name, int $speed = 0): object {
    $this->name = $name;
    $this->speed = $speed;
    return $this;
  }

}

