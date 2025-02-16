<?php
namespace Waxedphp\Waxedphp\Php;

class Vocab {

  private Base $base;
  private string $lang = 'en';
  private string $name = 'vocab';
  private string $path = '';
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
    $this->path = $this->base->getAppPath() . DIRECTORY_SEPARATOR . 'Language';
  }

  /**
  * setPath
  *
  * @param string $path
  * @return object
  */
  public function setPath(string $path): object {
    $this->path = realpath($path);
    return $this;
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
    //$fname = $this->base->getAppPath() . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->name . '.' . $this->lang . '.php';
    $fname = $this->path . DIRECTORY_SEPARATOR . $this->lang . DIRECTORY_SEPARATOR . $this->name . '.php';
    //print_r($fname);
    if (file_exists($fname)) {
       $v = include($fname);
       $this->_vocab_ = $v;
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
  
  public function get(?string $name = null, ?string $lang = null): array {
    if ($name) $this->load($name);
    if ($lang) $this->lang($lang);
    if (!$this->_loaded_==$this->name) {
      $this->_load();
    }
    return $this->_vocab_;
  }

}
