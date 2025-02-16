<?php
namespace Waxedphp\Waxedphp;



/*

if (basename(dirname(__FILE__)) == 'Waxed') {
  require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Autoloader.php';
} else if (is_dir(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Waxed')) {
  require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Waxed' . DIRECTORY_SEPARATOR . 'Autoloader.php';
} else die('Waxed engine is not properly installed.');
Waxed_Autoloader::register();
*/


class Waxed extends Php\Base {

  static $_android_js = false;

  public function __construct($cfg = false){
    parent::__construct('/waxed/design/waxed/');
    //define('ENVIRONMENT', 'development');
    //print_r(getcwd());

    if (is_array($cfg)) {
      //$this->setup($cfg);
    }

  }

  public function loadConfig($path = "waxed"){
    /*
    // In case we are used inside CodeIgniter 3:
    if (is_callable('get_instance')) {
      $this->ci = &get_instance();
      $this->ci->config->load($path);
      $this->setup($this->ci->config->item("waxed"));
    } else if (class_exists('Phalcon\Config')) {
      // In case we are used inside Phalcon:
      //$conf = DI::getDefault()->get('pconf');
      //$this->setup($conf->waxed);
      //configs/global
      $conf = new \Phalcon\Config\Adapter\Json(APP_PATH . $path . '.json');
      $this->setup($conf);


    }
    */

  }

  public function Image() {
    return new Php\Image();
  }

  public function Video() {
    return new Php\Video();
  }

  public function Resumable() {
    return new Php\Resumable();
  }

  public function Utils() {
    return new Php\Utils($this);
  }

  public function Mustache() {
    return new Php\Mustache($this);
  }

  public function dump_debug_info() {
    return $this->appPath;
  }

  public function AndroidJs() {
    if (!self::$_android_js) {
      self::$_android_js = new Php\AndroidJs($this);
    };
    return self::$_android_js;
  }

}
