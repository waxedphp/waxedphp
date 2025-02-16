<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Reload extends AbstractReaction {

  /**
   * @var ?int $onTime
   */
  private ?int $onTime = null;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'reload',
    );
    if($this->onTime>0){
      $a['ontime']=intval($this->onTime);
    };
    return $a;
  }

  function configure(int $onTime = 0): object {
    $this->onTime = $onTime;
    return $this;
  }

}

