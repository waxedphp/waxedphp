<?php
namespace Waxedphp\Waxedphp\Php\Setters;

class AceEditor extends AbstractSetter {

  /**
   * @var array<mixed> $setup
   */
  private array $setup = [
    'mode' => 'ace/mode/markdown',
    'theme' => 'ace/theme/tomorrow',
  ];

  function setValue($value) {
    $this->setup['value'] = $value;
    return $this;
  }

  function setMode($mode) {
    $this->setup['mode'] = 'ace/mode/'.$mode;
    //$this->setup['mode'] = $mode;
    return $this;
  }

  function setTheme($theme) {
    $this->setup['theme'] = 'ace/theme/'.$theme;
    //$this->setup['theme'] = $theme;
    return $this;
  }

  public function value(mixed $value = null): array {
    $a = $this->setup;
    if ($value) $a['value'] = $value;
    return $a;
  }

}
