<?php
namespace Waxedphp\Waxedphp\php\Setters;

class SlimSelect extends AbstractSetter {

  /**
   * @var array<mixed> $setup
   */
  private array $setup = [];

  /**
   * @var array<mixed> $data
   */
  private array $data = [];

  /**
  * set value
  *
  * @param int|string|array<mixed> $value
  * @return object
  */
  function setValue(int|string|array $value): object {
    $this->setup['value'] = $value;
    return $this;
  }

  /**
  * set data
  *
  * @param array<mixed> $data
  * @return object
  */
  function setData(array $data): object {
    $this->setup['data'] = $data;
    return $this;
  }

  /**
  * set opened
  *
  * @param bool $opened
  * @return object
  */
  function setOpened(bool $opened): object {
    $this->setup['opened'] = $opened;
    return $this;
  }

  /**
  * set enabled
  *
  * @param bool $enabled
  * @return object
  */
  function setEnabled(bool $enabled): object {
    $this->setup['enabled'] = $enabled;
    return $this;
  }

  /**
  * set setup
  *
  * @param array<mixed> $cfg
  * @return object
  */
  function setSetup(array $cfg): object {
    $this->setup = array_merge_recursive($this->setup, $cfg);
    return $this;
  }

  /**
  * get setup
  *
  * @return array<mixed>
  */
  function getSetup(): array {
    return $this->setup;
  }

  /**
  * set dataset
  *
  * @param string $label
  * @param array<mixed> $data
  * @param string $dd
  * @return object
  */
  function setDataset(string $label, array $data, string $dd = ''): object {
    $this->data = [];
    $data = self::_traverse($data, $dd);
    if (is_array($data)) {
      foreach ($data as $row) {
        $this->data[] = $row;
      };
    };
    return $this;
  }

  /**
  * for slim select
  *
  * @return array<mixed>
  */
  public function forSlimSelect(): array {
    $a = $this->setup;
    return $a;
  }

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value): array {
    $this->setValue($value);
    return $this->forSlimSelect();
  }

}
