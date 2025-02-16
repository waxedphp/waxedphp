<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class Redirect extends AbstractReaction {
  /**
   * @var string $url
   */
  private string $url = '';
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
      'action' => 'gourl',
      'url' => $this->url
    );
    if($this->onTime>0){
      $a['ontime']=intval($this->onTime);
    };
    return $a;
  }

  function configure(string $url, int $onTime = 0): object {
    $this->url = $url;
    $this->onTime = $onTime;
    return $this;
  }

}

