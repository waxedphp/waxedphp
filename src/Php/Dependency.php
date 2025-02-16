<?php

namespace Waxedphp\Waxedphp\Php;

class Dependency {

  /**
   * @var string $name
   */
  protected string $name = '';
  /**
   * @var Base $base
   */
  protected ?Base $base;
  /**
   * @var string $packagePath
   */
  protected ?string $packagePath = null;
  
  public function __construct(?Base $base, string $name){
    $this->base = $base;
    $this->name = $name;
    $this->findSelf();
  }
  
  protected function findSelf() {
    $composerPackage = 'waxedphp/' . $this->name;
    $f = null;
    if (\Composer\InstalledVersions::isInstalled($composerPackage)) {
      $f = \Composer\InstalledVersions::getInstallPath($composerPackage);
      if ($f) $f .= '/pkg';
    }
    if ((is_null($f))||(!is_dir($f))) {
      $packagePath = realpath(
        dirname(__FILE__) . DIRECTORY_SEPARATOR
        . '..' . DIRECTORY_SEPARATOR
        . '..' . DIRECTORY_SEPARATOR
        . 'pkg') . DIRECTORY_SEPARATOR;
      $f = $packagePath . $this->name;
    }
    if (!is_dir($f)) {
      $this->packagePath = null;
      return;
    }
    $this->packagePath = $f;
  }
  
  public function getAllFiles() {
    if (!$this->packagePath) return [];
    //$json = $f . DIRECTORY_SEPARATOR . 'dependency.json';
    $ff=$this->packagePath . DIRECTORY_SEPARATOR . 'dependency.php';
    if (!is_file($ff)) return [];
    $waxed = $this->base;
    if (!defined("NODE")) define("NODE",$this->base->plugin->getNodeJSPath());
    if (!defined("DESIGN")) define("DESIGN", $this->base->design->getDesignPath());
    $PATH = $this->packagePath;
    $a = include($ff);
    $a['PATH'] = $this->packagePath;
    //file_put_contents($json, json_encode($a));
    return $a;
  }
  
  function getPath():string {
    if (!$this->packagePath) return '';
    return realpath($this->packagePath);
  }

  function getFiles(string $type):array {
    if (!$this->packagePath) return [];
  }


  /**
  * get_example_data
  *
  * @return array<mixed>
  */
  public function get_example_data(): array {
    if (!$this->packagePath) return [];
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'example.php';
    if (!is_file($f)) return [];
    $waxed = $this->base;
    return require($f);
  }

  /**
  * get_example_html
  *
  * @return string
  */
  public function get_example_html(): string {
    if (!$this->packagePath) return '';
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'example.html';
    if (!is_file($f)) return '';
    return file_get_contents($f) . $x;
  }

  /**
  * get_source
  *
  * @param string $name
  * @return string
  */
  public function get_source(): string {
    $x = '# ' . $this->name;
    $f=$this->packagePath;
    if (!is_dir($f)) return $x;
    $f.=DIRECTORY_SEPARATOR . 'source.txt';
    if (!is_file($f)) return $x;
    return trim((String)file_get_contents($f));
  }

  /**
  * get_documentation
  *
  * @return string
  */
  public function get_documentation(): string {
    $x = '# ' . $this->name;
    if (!$this->packagePath) return $x;
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'documentation.md';
    if (!is_file($f)) return $x;
    return (String)file_get_contents($f);
  }

  /**
  * get_license
  *
  * @return string
  */
  public function get_license(): string {
    $x = '# ' . $this->name;
    if (!$this->packagePath) return $x;
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'LICENSE';
    if (!is_file($f)) return $x;
    return (String)file_get_contents($f);
  }

  /**
  * get_dependency
  *
  * @return array<mixed>
  */
  public function get_dependency(): array {
    if (!$this->packagePath) return [];
    //$json = $f . DIRECTORY_SEPARATOR . 'dependency.json';
    $ff=$this->packagePath . DIRECTORY_SEPARATOR . 'dependency.php';
    if (!is_file($ff)) return [];
    $waxed = $this->base;
    if (!defined("NODE")) define("NODE",$this->getNodeJSPath());
    if (!defined("DESIGN")) define("DESIGN", $this->base->design->getDesignPath());
    $PATH = $f;
    $a = include($ff);
    $a['PATH'] = $f;
    //file_put_contents($json, json_encode($a));
    return $a;
  }

  /**
  * get_inside
  *
  * @return array<mixed>
  */
  public function get_inside(): array {
    if (!$this->packagePath) return [];
    //$json = $f . DIRECTORY_SEPARATOR . 'inside.json';
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'inside.php';
    if (!is_file($f)) return [];
    $waxed = $this->base;
    if (!defined("NODE")) define("NODE", $this->getNodeJSPath());
    if (!defined("DESIGN")) define("DESIGN", $this->base->design->getDesignPath());
    $a = include($f);
    return $a;
  }


  /**
  * get_setter
  *
  * @return ?object
  */
  public function get_setter(): ?object {
    if (!$this->packagePath) return null;
    //$json = $f . DIRECTORY_SEPARATOR . 'inside.json';
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'setter.php';
    if (!is_file($f)) return null;
    include_once($f);
    $className = '\\Waxedphp\\Waxedphp\\Php\\Setters\\' . $name . '\\Setter';
    return new $className($this->base);
  }

  public function getInstallScript(): ?string {
    if (!$this->packagePath) return null;
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'install.sh';
    if (!is_file($f)) return null;
    return file_get_contents($f);
  }

  public function doInstallScript(): ?string {
    if (!$this->packagePath) return null;
    $f = $this->packagePath . DIRECTORY_SEPARATOR . 'install.sh';
    if (!is_file($f)) return null;
    $re = [];
    exec($f, $re);
    print_r($re);
    return file_get_contents($f);
  }

}
