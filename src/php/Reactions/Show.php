<?php
namespace JasterStary\Waxed\php\Reactions;

class Show extends Hide {
  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'show',
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

}

