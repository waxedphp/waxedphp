<?php
namespace JasterStary\Waxed\php\Reactions;

class Init extends AbstractReaction {

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'init',
      'engine' => $this->base->getEngine(),
    );
    if ($this->base->getChunked()) {
      $a['polling'] = true;
    };
    $a['routes'] = [
      'action' => $this->base->getAjaxUrl(),
      'design' => $this->base->design->getDesignRoute(),
      'plugin' => $this->base->plugin->getPluginRoute(),
    ];
    return $a;
  }

  /**
  * configure
  *
  * @return object
  */
  function configure():object {
    return $this;
  }

}

