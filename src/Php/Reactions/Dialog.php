<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Dialog extends AbstractReaction {
  /**
   * @var array<mixed> $RECORD
   */
  private array $RECORD = [];
  /**
   * @var ?string $template
   */
  private ?string $template = null;
  /**
   * @var ?string $whereId
   */
  private ?string $whereId = null;
  /**
   * @var ?int $onTime
   */
  private ?int $onTime = null;
  /**
   * @var bool $append
   */
  private bool $append = false;
  /**
   * @var ?string $cls
   */
  private ?string $cls = null;
  /**
   * @var ?string $signature
   */
  private ?string $signature = null;
  /**
   * @var int $timeout
   */
  private int $timeout = 0;
  /**
   * @var bool $modal
   */
  private bool $modal = false;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'dialog',
      'template' => $this->base->design->getRoute($this->template),
      'RECORD' => $this->RECORD,
    );
    if(is_numeric($this->onTime)) {
      $a['ontime']=intval($this->onTime);
    };
    if(is_string($this->whereId)){
      $a['element']='#'.$this->whereId;
    };
    if($this->append){
      $a['append']=true;
    };

    if(is_string($this->cls)){
      $a['class'] = $this->cls;
    };
    if(is_string($this->signature)){
      $a['signature'] = $this->signature;
    };
    if($this->timeout){
      $a['timeout']=intval($this->timeout);
    };
    if($this->modal){
      $a['modal'] = $this->modal;
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
  * @param ?string $template
  * @param int $timeout
  * @param ?string $class
  * @param bool $modal
  * @param string $signature
  * @return object
  */
  function configure(array $RECORD, ?string $template = null, int $timeout = 0, ?string $class = null, bool $modal = false, string $signature = 'modal'):object {

    $this->RECORD = array_merge($this->base->getDefaults(),$RECORD);
    $this->base->vocab->translate($this->RECORD);

    if(is_string($template)){
      $this->template=$template;
    };
    if(is_string($class)){
      $this->cls=$class;
    };
    if(is_string($signature)){
      $this->signature=$signature;
    };
    if($timeout){
      $this->timeout=intval($timeout);
    };
    if($modal){
      $this->modal=$modal;
    };
    /*
    if(is_numeric($onTime)){
      $this->ontime=intval($onTime);
    };
    if(is_string($whereId)){
      $this->element='#'.$whereId;
    };
    if($append){
      $this->append=true;
    };
    */
    return $this;
  }


}

