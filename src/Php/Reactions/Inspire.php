<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Inspire extends AbstractReaction {

  /**
   * @var array<mixed> $RECORD
   */
  private array $RECORD = [];
  /**
   * @var ?int $onTime
   */
  private ?int $onTime = null;
  /**
   * @var int $timeout
   */
  private int $timeout = 0;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'inspire',
      'RECORD' => $this->RECORD,
    );
    if ($this->timeout > 0) {
      $a['timeout'] = $this->timeout;
    };
    if ($this->onTime > 0) {
      $a['ontime'] = $this->onTime;
    };
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    return $a;
  }

  /**
  * configure
  *
  * @param array<mixed> $RECORD
  * @param int $timeout
  * @return object
  */
  function configure(array $RECORD, int $onTime = 0): object {
    $this->RECORD = array_merge($this->base->getDefaults(),$RECORD);
    $this->base->vocab->translate($this->RECORD);
    if ($onTime>0) {
      $this->onTime = intval($onTime);
    }
    /*
    if($append){
      $this->append=true;
    };
    */
    return $this;
  }


}

