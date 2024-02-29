<?php
namespace Waxedphp\Waxedphp;


class Waxed extends Base {

  static $_android_js = false;

  public function __construct($cfg = false){
    parent::__construct('/waxed/design/waxed/');
    if (is_array($cfg)) {
    }
  }

  public function loadConfig($path = "waxed"){

  }

  public function Image() {
    return new Image();
  }

  public function Video() {
    return new Video();
  }

  public function Resumable() {
    return new Resumable();
  }

  public function Utils() {
    return new Utils($this);
  }

  public function Mustache() {
    return new Mustache($this);
  }

  public function dump_debug_info() {
    return $this->appPath;
  }

  public function AndroidJs() {
    if (!self::$_android_js) {
      self::$_android_js = new AndroidJs($this);
    };
    return self::$_android_js;
  }

}
