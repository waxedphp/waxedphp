<?php
namespace Waxedphp\Waxedphp\Php;

class Config {

  private bool $development = false;

  private bool $freezed = false;

  private bool $chunked = false;

  private string $engine = 'mark2';

  private string $design_route = "/html/";

  private string $assets_route = "/assets/";

  private string $action_route = "/{ROUTE}/action";

  private string $plugin_route = "/{ROUTE}/plugin/";

  private string $design_path = "../../public/html/";

  private string $nodejs_path = "../../node_modules/";

  private string $vendor_path = "../vendor/";

  private string $writable_path = "../../public/assets/";

  private string $vocab_path = "../app/Language/";

  private string $waxedPhpDir = "";

  private string $dataDir = "";

  private array $keys = [
      "freezed",
      "chunked",
      "engine",
      "design_route",
      "action_route",
      "plugin_route",
      "assets_route",
      "design_path",
      "nodejs_path",
      "vendor_path",
      "writable_path",
      "vocab_path",
      "development",
    ];


  public function __construct() {
    $this->waxedPhpDir = dirname(\Composer\InstalledVersions::getInstallPath('waxedphp/waxedphp'));
    $dataDir = $this->waxedPhpDir  . '/.data/';
    $this->dataDir = $dataDir;

    if ((is_dir($dataDir))&&(is_file($dataDir . '/config.json'))) {
      $this->setConfig(json_decode(file_get_contents($dataDir . '/config.json'), true));
      return;
    }
    $this->vendor_path = realpath(dirname($this->waxedPhpDir));
    $frameworkDir = realpath(dirname($this->vendor_path));
    $baseDir = realpath(dirname($frameworkDir));
    $entryDir = $this->getEntryDir($baseDir);
    $this->design_path = $entryDir . '/html/';
    $this->writable_path = $entryDir . '/assets/';
    $this->nodejs_path = $baseDir . '/node_modules/';
    $this->save();
  }

  private function getEntryDir(string $baseDir) {
    $btr = debug_backtrace();
    $e = end($btr);
    if (isset($e['file'])) {
      return realpath(dirname($e['file']));
    };
    if (is_dir($baseDir . '/web')) return $baseDir . '/web';
    return $baseDir . '/public';
  }

  public function save() {
    if (!is_dir($this->dataDir)) @mkDir($this->dataDir);
    file_put_contents($this->dataDir . '/config.json', json_encode($this->getConfig(), JSON_PRETTY_PRINT));
  }

  public function setConfig(array $arr) {
    foreach ($this->keys as $key) {
      if (isset($arr[$key])) {
        $this->$key = $arr[$key];
      }
    }
  }

  public function getConfig():array {
    $arr = [];
    foreach ($this->keys as $key) {
      if (isset($this->$key)) {
        $arr[$key] = $this->$key;
      }
    }
    return $arr;
  }

  public function getDataDir():string {
    return $this->dataDir;
  }

  public function getWaxedPhpDir():string {
    return $this->waxedPhpDir;
  }

  public function getAllUses():array {
    $re = [];
    $f = $this->getDataDir() . '/uses.json';
    if ((is_file($f)) && (is_writable($f))) {
      $a = json_decode(file_get_contents($f), true);
      foreach ($a as $k => $v) $re[$v] = 1;
    }
    ksort($re);
    return array_keys($re);
  }

}
