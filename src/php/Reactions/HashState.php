<?php
namespace JasterStary\Waxed\php\Reactions;

class HashState extends AbstractReaction {
  /**
   * @var string $hash
   */
  private string $hash = '';
  /**
   * @var int $onTime
   */
  private int $onTime = 0;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'hashState',
      'state' => $this->hash,
    );
    if($this->onTime) {
      $a['ontime']=intval($this->onTime);
    };
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    return $a;
  }

  /**
  * configure
  *
  * @param string $hash
  * @param int $onTime
  * @return object
  */
  function configure(string $hash, int $onTime = 0):object {

    if(is_string($hash)){
      $this->hash=$hash;
    };
    if($onTime){
      $this->onTime=intval($onTime);
    };
    return $this;
  }

}
