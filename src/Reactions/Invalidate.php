<?php
namespace Waxedphp\Waxedphp\Reactions;

class Invalidate extends AbstractReaction {

  /**
   * @var array<mixed> $RECORD
   */
  private array $RECORD = [];
  /**
   * @var ?string $whereId
   */
  private ?string $whereId = null;
  /**
   * @var ?string $template
   */
  private ?string $template = null;
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
      'action' => 'invalidate',
      'RECORD' => $this->RECORD,
    );
    if(is_string($this->whereId)){
      $a['element']='#'.$this->whereId;
    };
    if ($this->onTime) {
      $a['ontime'] = intval($this->onTime);
    };
    if ($this->template) {
      $a['template'] = $this->template;
    };
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    return $a;
  }

  /**
  * configure
  *
  * @param object|array<mixed> $inputs
  * @param ?string $template
  * @param ?string $whereId
  * @return object
  */
  function configure(array|object $inputs = [], ?string $template = null, ?string $whereId = null): object {

    if ((is_object($inputs)) && (method_exists($inputs, 'getMessages'))) {
      $this->RECORD = $inputs->getMessages();
    } else if ((is_object($inputs)) && (method_exists($inputs, 'getMessage'))) {
      $err = trim($inputs->getMessage());
      if (strpos($err, '{') === 0) {
        $this->RECORD = json_decode($err, true);
      } else {
         //$err.=' (file:'.$inputs->getFile().' line:'.$inputs->getLine().')';
         $this->RECORD = ['error' => $err];
      }
    } else if (is_array($inputs)) {
      $this->RECORD = $inputs;
    } else {
      $this->RECORD = [
        'error' => 'unknown',
      ];
    }

    if(is_string($template)){
      $this->template=$template;
    };
    if(is_string($whereId)){
      $this->whereId='#'.$whereId;
    };
    /*
    if($append){
      $this->append=true;
    };
    */
    return $this;
  }


}

