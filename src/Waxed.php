<?php
namespace Waxedphp\Waxedphp;


class Waxed extends php\Base {

  static $_android_js = false;

  public function __construct($cfg = false){
    parent::__construct('/waxed/design/waxed/');
    if (is_array($cfg)) {
    }
  }

  public function loadConfig($path = "waxed"){

  }

  public function Image() {
    return new php\Image();
  }

  public function Video() {
    return new php\Video();
  }

  public function Resumable() {
    return new php\Resumable();
  }

  public function Utils() {
    return new php\Utils($this);
  }

  public function Mustache() {
    return new php\Mustache($this);
  }

  public function dump_debug_info() {
    return $this->appPath;
  }

  public function AndroidJs() {
    if (!self::$_android_js) {
      self::$_android_js = new php\AndroidJs($this);
    };
    return self::$_android_js;
  }

}
