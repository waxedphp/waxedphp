<?php
namespace Waxedphp\Waxedphp\Setters;

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
    return $this;
  }

  function setTheme($theme) {
    $this->setup['theme'] = 'ace/theme/'.$theme;
    return $this;
  }

  public function value(mixed $value): array {
    $a = $this->setup;
    $a['value'] = $value;
    return $a;
  }

}
