<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Hide extends AbstractReaction {
  /**
   * @var string $whereId
   */
  protected ?string $whereId = null;
  /**
   * @var ?int $onTime
   */
  protected ?int $onTime = null;
  /**
   * @var ?string $picked
   */
  protected ?string $picked = null;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'hide',
    );
    if($this->whereId){
      $a['element']=$this->whereId;
    };
    if($this->onTime>0){
      $a['timeout']=intval($this->onTime);
    };
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
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
      $this->picked = $id;
    }
    return $this;
  }

  /**
  * configure
  *
  * @param ?string $whereId
  * @param int $onTime
  * @return object
  */
  function configure(?string $whereId = null, int $onTime = 0): object {
    $this->whereId = $whereId;
    $this->onTime = $onTime;
    return $this;
  }

}

