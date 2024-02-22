<?php
namespace Waxedphp\Waxedphp\php\Reactions;

class LoadPlugins extends AbstractReaction {
  /**
   * @var ?string $plug
   */
  private ?string $plug = null;

  /**
  * to array
  *
  * @return array<mixed>
  */
  function toArray(): array {
    $a = array(
      'action' => 'plug',
    );
    if (is_string($this->plug)) {
      $route = $this->base->plugin->getPluginRoute();
      $a['data'] = array(
        'js'=>[$route . $this->plug.'.js'],
        'css'=>[$route . $this->plug.'.css?'.$this->base->design->getStyle()],
      );
    };
    return $a;
  }

  /**
  * configure
  *
  * @param string|array<string> $plug
  * @return object
  */
  function configure(string|array $plug):object {
    if (is_array($plug)) {
      $plug = implode('-', $plug);
    };
    $this->plug = $plug;
    return $this;
  }

}

