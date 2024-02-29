<?php
namespace Waxedphp\Waxedphp;

class Vocab {

  private Base $base;
  private string $lang = 'en';
  private string $name = 'vocab';
  /**
   * @var array<mixed> $_vocab_
   */
  private array $_vocab_ = [];
  private ?string $_loaded_ = null;

  /**
  * constructor
  *
  * @param Base $base
  */
  public function __construct(Base $base){
    $this->base = &$base;
  }

  /**
  * lang
  *
  * @param string $KEY
  * @return object
  */
  public function lang(string $KEY): object {
    $this->lang = $KEY;
    return $this;
  }

  /**
  * load
  *
  * @param string $NAME
  * @return object
  */
  public function load(string $NAME): object {
    $this->name = $NAME;
    return $this;
  }

  /**
  * _load
  *
  * @return object
  */
  private function _load(): object {
    $this->_vocab_ = [];
    $fname = $this->base->getAppPath() . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->name . '.' . $this->lang . '.php';
    if (file_exists($fname)) {
      $this->_vocab_ = require($fname);
    };
    $this->_loaded_ = $this->name;
    return $this;
  }

  /**
  * translate
  *
  * @param array<mixed> $RECORD
  * @return object
  */
  public function translate(&$RECORD): object {
    if (!isset($RECORD['VOCAB'])) return $this;
    $VOCAB = $RECORD['VOCAB'];
    if (!is_string($VOCAB)) return $this;
    if (!$this->_loaded_==$this->name) $this->_load();
    if (!isset($this->_vocab_[$VOCAB])) return $this;
    $p = [];
    foreach ($this->_vocab_[$VOCAB] as $k=>$v) {
      if (isset($RECORD[$k])) {
        $p[] = $k;
      }
    };
    if (!empty($p)) {
      //log_message('error', 'VOCAB:' . $VOCAB . ':' . implode(',', $p));
    };
    $RECORD = array_merge($this->_vocab_[$VOCAB], $RECORD);
    return $this;
  }

}
