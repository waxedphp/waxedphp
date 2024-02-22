<?php
namespace JasterStary\Waxed\php\Reactions;

class Load extends AbstractReaction {

  /**
   * @var array<mixed> $RECORD
   */
  private array $RECORD = [];
  /**
   * @var ?string $url
   */
  private ?string $url = null;
  /**
   * @var ?string $action
   */
  private ?string $action = null;

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
      'url' => $this->base->getAjaxUrl(),
      'action' => 'load',
      'data' => [],
    );
    if (is_string($this->url)) {
      $a['url'] = $this->url;
    };
    if (is_string($this->action)) {
      $a['data']['action'] = $this->action;
    };
    if (!empty($this->RECORD)) {
      $a['data'] = array_merge($this->RECORD, $a['data']);
    };
    if($this->onTime>0){
      $a['ontime']=intval($this->onTime);
    };
    return $a;
  }

  /**
  * configure
  *
  * @param ?string $action
  * @param array<mixed> $RECORD
  * @param ?string $url
  * @param int $onTime
  * @return object
  */
  function configure(?string $action = null, array $RECORD = [], string $url = null, int $onTime = 0): object {
    $this->RECORD = $RECORD;
    $this->action = $action;
    $this->url = $url;
    $this->onTime = $onTime;
    return $this;
  }

}

