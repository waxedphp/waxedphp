<?php
namespace JasterStary\Waxed\php\Reactions;

class DialogClose extends AbstractReaction {
  /**
   * @var string $signature
   */
  private string $signature;
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
      'action' => 'dialog/close',
      'signature' => $this->signature
    );
    if($this->onTime>0){
      $a['timeout']=intval($this->onTime);
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

  function configure(string $signature, int $onTime = 0): object {
    $this->signature = $signature;
    $this->onTime = $onTime;
    return $this;
  }

}

