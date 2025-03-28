<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Display extends AbstractReaction {

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

  private bool $giveBlock = false;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    if ($this->giveBlock) {
      $a = array(
        'action' => 'inspire',
        'RECORD' => $this->RECORD,
      );
    } else {
      $a = array(
        'action' => 'display',
        'template' => $this->base->design->getRoute($this->template),
        'RECORD' => $this->RECORD,
      );
      if(is_numeric($this->onTime)) {
        $a['ontime']=intval($this->onTime);
      };
    };
    if(is_string($this->whereId)){
      //$a['element']='#'.$this->whereId;
    };
    if($this->append){
      $a['append']=true;
    };
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    return $a;
  }

  function toBlock(array &$a) {
    if ($this->giveBlock) {
      $a[$this->picked] = $this->base->reaction->renderBlock($this->RECORD, $this->template);
    }
  }

  /**
  * configure
  *
  * @param array<mixed> $RECORD
  * @param ?string $template
  * @param ?int $onTime
  * @param ?string $whereId
  * @param bool $append
  * @return object
  */
  function configure(array $RECORD, ?string $template = null, int|bool $onTime = false, ?string $whereId = null, bool $append = false): object {

    $this->RECORD = array_merge($this->base->getDefaults(),$RECORD);
    $this->base->vocab->translate($this->RECORD);

    if(is_string($template)){
      $this->template = $template;
    };
    if(is_numeric($onTime)){
      $this->onTime = intval($onTime);
    } else if(is_bool($onTime)){
      $this->giveBlock = $onTime;
    };
    if(is_string($whereId)){
      $this->whereId = '#' . $whereId;
    };
    if($append){
      $this->append = true;
    };

    return $this;
  }


}

