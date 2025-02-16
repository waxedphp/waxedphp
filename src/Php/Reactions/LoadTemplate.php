<?php
namespace Waxedphp\Waxedphp\Php\Reactions;

class LoadTemplate extends AbstractReaction {
  /**
   * @var string $name
   */
  private string $name;
  /**
   * @var string $html
   */
  private string $html;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'loadTemplate',
      'name' => $this->base->design->getRoute($this->name),
      'html' => $this->html,
    );
    return $a;
  }

  /**
  * configure
  *
  * @param string $html
  * @return object
  */
  function configure(string $name, string $html):object {
    $this->name = $name;
    $this->html = $html;
    return $this;
  }

}

