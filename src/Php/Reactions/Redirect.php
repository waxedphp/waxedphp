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
   * @var ?bool $hard
   */
  private ?bool $hard = null;
  
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
    if($this->hard){
      $a['hard']=true;
    };
    return $a;
  }

  function configure(string $url, int $onTime = 0, bool $hard = false): object {
    $this->url = $url;
    $this->onTime = $onTime;
    $this->hard = $hard;
    return $this;
  }

}

