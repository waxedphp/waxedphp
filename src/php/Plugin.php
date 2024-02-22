<?php
namespace Waxedphp\Waxedphp\php;

use MatthiasMullie\Minify;

class Plugin {
  /**
   * @var Base $base
   */
  protected Base $base;
  /**
   * @var string $packagePath
   */
  protected string $packagePath = '';
  /**
   * @var string $nodeJsPath
   */
  protected string $nodeJsPath = '';
  /**
   * @var string $writablePath
   */
  protected string $writablePath = '';
  /**
   * @var array<mixed> $units
   */
  protected array $units = [
    'js' => [],
    'css' => [],
  ];
  /**
   * @var array<mixed> $_uses_
   */
  protected array $_uses_ = [];
  /**
   * @var string $mode
   */
  protected string $mode = 'html';
  /**
   * @var string $route
   */
  protected string $route = '/include/';

  protected mixed $cache = false;

  /**
  * constructor
  *
  * @param Base $base
  */
  public function __construct(Base $base){
    $this->base = $base;
    $this->packagePath = realpath(
      dirname(__FILE__)
      . DIRECTORY_SEPARATOR
      . '..'
      . DIRECTORY_SEPARATOR
      . 'pkg'
    ). DIRECTORY_SEPARATOR;
  }

  /**
  * set writable path
  *
  * @param string $writablePath
  * @return Plugin
  */
  public function setWritablePath(string $writablePath): Plugin {
    $rpath = realpath($writablePath);
    if (!$rpath) throw new \Exception('Wrong writable path.');
    $this->writablePath = $rpath;
    //print_r($this->writablePath);die();
    return $this;
  }

  /**
  * set plugin route
  *
  * @param string $route
  * @return Plugin
  */
  public function setPluginRoute(string $route): Plugin {
    //print_r($route);
    $this->route = $route;
    return $this;
  }

  /**
  * get plugin route
  *
  * @return string
  */
  public function getPluginRoute(): string {
    return $this->route;
  }

  /**
  * get node js path
  *
  * @return string
  */
  public function getNodeJsPath(): string {
    return $this->nodeJsPath;
  }

  /**
  * set node js path
  *
  * @param string $nodeJsPath
  * @return Plugin
  */
  public function setNodeJsPath(string $nodeJsPath): Plugin {
    $rpath = realpath($nodeJsPath);
    if (!$rpath) throw new \Exception('Wrong NODE JS path.');
    $this->nodeJsPath = $rpath;

    return $this;
  }

  /**
  * set writable path
  *
  * @param string $writablePath
  * @return Plugin
  public function setWritablePath(string $writablePath): Plugin {
    $rpath = realpath($writablePath);
    if (!$rpath) throw new \Exception('Wrong writable path.');
    $this->writablePath = $rpath;
    return $this;
  }
  */

  /**
  * uses
  *
  * @param array<string>|string $listing
  * @return Plugin
  */
  public function uses(array|string $listing): Plugin {
    $_uses_ = func_get_args();
    if (is_array($_uses_[0])) {
      $_uses_ = $_uses_[0];
    };
    $this->_uses_ = [];
    foreach ($_uses_ as $k => $v) {
      $this->_uses_[$v] = $v;
    };
    ksort($this->_uses_);
    if (isset($this->_uses_['base'])) {
      $this->_uses_ = ['base'=>'base']+$this->_uses_;
    }
    if (isset($this->_uses_['design'])) {
      unset($this->_uses_['design']);
      $this->_uses_ = $this->_uses_+['design'=>'design'];
    }
    $this->_uses_ = array_keys($this->_uses_);
    return $this;
  }

  public function getUsesAsString() {
    return implode('-', $this->_uses_);
  }

  /**
  * has build
  *
  * @return bool
  */
  public function has_build(): bool {
    if (!$this->writablePath) throw new \Exception('Writable path is not set.');
    $path = $this->writablePath . '/' . implode('-', $this->_uses_);
    if (!is_dir($path)) return false;
    return is_file($path . '/main.js');
  }

  /**
  * _is_production
  *
  * @return bool
  protected function _is_production(): bool {
    return !$this->base->is_development();
  }
  */

  /**
  * _nocache
  *
  * @return bool
  */
  protected function _nocache(): bool {
    if ($this->cache) return true;
    return false;
  }

  /**
  * _inside
  *
  * @param string $plugin
  * @param string $file
  * @param string $ext
  * @return Plugin
  */
  protected function _inside(string $plugin, string $file, string $ext): Plugin {
    throw new \Exception('Development functions not allowed.');
  }

  /**
  * mode
  *
  * @param string $mode
  * @return Plugin
  */
  public function mode(string $mode): Plugin {
    $this->mode = $mode;
    return $this;
  }

  /**
  * get units
  *
  * @return array<mixed>
  */
  public function getUnits(): array {
    return $this->units;
  }

  /**
  * dispatch
  *
  * @param string $url
  * @param array<mixed> $params
  * @return void
  */
  public function dispatch(string $url, array $params = []): void {
    throw new \Exception('Development functions not allowed.');
  }

  /**
  * js
  *
  * @return string
  */
  public function JS(): string {
    $s = '';
    switch ($this->mode) {
      case 'html': //$this->route .
        $s.='<script type="text/javascript" src="';
        $s.='/assets/';
        $s.=implode('-', $this->_uses_);
        $s.='/main.js';
        if ($this->_nocache()) {
          $s.='?d='.date('YmdHis');
        }
        $s.='" ></script>';
        return $s;
      //
      //case 'html-include':
      //  return '<script type="text/javascript" >' . $this->load('js', $this->units) . '</script>';
      //case 'js':
      //  return $this->load('js', $this->units);
      //
    }
    return $s;
  }

  /**
  * css
  *
  * @return string
  */
  public function CSS(): string {
    $s = '';
    switch ($this->mode) {
      case 'html'://$this->route .
        $s .= '<link rel="stylesheet" type="text/css" href="';
        $s.='/assets/';
        $s .= implode('-', $this->_uses_);
        $s .= '/main.css?';
        //$s .= $this->base->design->getStyleQuery();
        if ($this->_nocache()) {
          $s .= '&d='.date('YmdHis');
        }
        $s .= '" />';
        return $s;
      //case 'html-include':
      //  return '<style>' . $this->load('css', $this->units) . '</style>';
      //case 'css':
      //  return $this->load('css', $this->units);
    }
    return $s;
  }

  /**
  * get
  *
  * @param string $content
  * @return string
  */
  public function GET($content = ''): string {
    /*
    switch ($this->mode) {
      case 'js':
        header("Content-type: application/javascript");
        if ($content) return $content;
        return $this->load('js', $this->units);
      case 'json':
        header("Content-type: application/json");
        if ($content) return $content;
        return $this->load('json', $this->units);
      case 'css':
        header("Content-type: text/css");
        if ($content) return $content;
        return $this->load('css', $this->units);
      case 'eot':
        header("Content-type: font/ttf");
        if ($content) return $content;
        return $this->load('eot', $this->units);
      case 'ttf':
        header("Content-type: font/ttf");
        if ($content) return $content;
        return $this->load('ttf', $this->units);
      case 'woff':
        header("Content-type: font/woff");
        if ($content) return $content;
        return $this->load('woff', $this->units);
      case 'woff2':
        header("Content-type: font/woff2");
        if ($content) return $content;
        return $this->load('woff2', $this->units);
      case 'otf':
        header("Content-type: font/otf");
        if ($content) return $content;
        return $this->load('otf', $this->units);
      case 'svg':
        header("Content-type: image/svg+xml");
        if ($content) return $content;
        return $this->load('svg', $this->units);
      case 'gif':
        header("Content-type: image/gif");
        if ($content) return $content;
        return $this->load($this->mode, $this->units);
      case 'png':
        header("Content-type: image/png");
        if ($content) return $content;
        return $this->load($this->mode, $this->units);
      case 'jpg':
      case 'jpeg':
        header("Content-type: image/jpeg");
        if ($content) return $content;
        return $this->load($this->mode, $this->units);
      case 'map':
        header("Content-type: text/plain");
        if ($content) return $content;
        return $this->load($this->mode, $this->units);
      case 'dbg':
        header("Content-type: text/plain");
        if ($content) return $content;
        return print_r($this->units, true);
    }
    */
    return '';
  }

  /**
  * load_css
  *
  * @param array<mixed> $arr
  * @return string
  */
  public function load_css(array $arr): string {
    return '';//$this->load('css', $arr);
  }

  /**
  * load_js
  *
  * @param array<mixed> $arr
  * @return string
  */
  public function load_js(array $arr): string {
    return '';//$this->load('js', $arr);
  }

  /**
  * get_list
  *
  * @return array<mixed>
  */
  public function get_list(): array {
    $a = [];
    $aa = scandir($this->packagePath);
    if (!$aa) {
      throw new \Exception('Wrong package path.');
    };
    foreach ($aa as $p) {
      $b = $this->get_dependency($p);
      if (!empty($b)) $a[$p] = [
        'title' => $p,
        //'dependency' => $b
      ];
      /*
      if (($p[0] != '.') && (is_dir($this->packagePath . $p)) && (is_file($this->packagePath . $p. '/waxed.json'))) {
        $a[$p] = ['name' => $p];
        if (is_file($this->packagePath . $p . '/waxed.json')) {
          $a[$p] = json_decode(file_get_contents($this->packagePath . $p . '/waxed.json'));
        };
      };
      */
    };
    return array_values($a);
  }

  /**
  * get_js
  *
  * @return array<int|string>
  */
  public function get_js(): array {
    return array_keys($this->units['js']);
  }

  /**
  * get_css
  *
  * @return array<int|string>
  */
  public function get_css(): array {
    return array_keys($this->units['css']);
  }

  /**
  * get_example_data
  *
  * @param string $name
  * @return array<mixed>
  */
  public function get_example_data(string $name): array {
    $x = [];
    $f=$this->packagePath.$name;
    if (!is_dir($f)) return $x;
    $f.=DIRECTORY_SEPARATOR . 'example.php';
    if (!is_file($f)) return $x;
    $waxed = $this->base;
    return require($f);
  }

  /**
  * get_example_html
  *
  * @param string $name
  * @return string
  */
  public function get_example_html(string $name): string {
    $x = '';
    //$x = '{{{docs}}}';//<div class="jsonviewer" ></div>';
    $f=$this->packagePath.$name;
    if (!is_dir($f)) return $x;
    $f.=DIRECTORY_SEPARATOR . 'example.html';
    if (!is_file($f)) return $x;
    return file_get_contents($f) . $x;
  }

  /**
  * get_source
  *
  * @param string $name
  * @return string
  */
  public function get_source(string $name): string {
    $x = '# ' . $name;
    $f=$this->packagePath.$name;
    if (!is_dir($f)) return $x;
    $f.=DIRECTORY_SEPARATOR . 'source.txt';
    if (!is_file($f)) return $x;
    return trim((String)file_get_contents($f));
  }

  /**
  * get_documentation
  *
  * @param string $name
  * @return string
  */
  public function get_documentation(string $name): string {
    $x = '# ' . $name;
    $f=$this->packagePath.$name;
    if (!is_dir($f)) return $x;
    $f.=DIRECTORY_SEPARATOR . 'documentation.md';
    if (!is_file($f)) return $x;
    return (String)file_get_contents($f);
  }

  /**
  * get_license
  *
  * @param string $name
  * @return string
  */
  public function get_license(string $name): string {
    $x = '# ' . $name;
    $f=$this->packagePath.$name;
    if (!is_dir($f)) return $x;
    $f.=DIRECTORY_SEPARATOR . 'LICENSE';
    if (!is_file($f)) return $x;
    return (String)file_get_contents($f);
  }

  /**
  * get_dependency
  *
  * @param string $name
  * @return array<mixed>
  */
  public function get_dependency(string $name): array {
    $f=$this->packagePath.$name;
    if (!is_dir($f)) return [];
    //$json = $f . DIRECTORY_SEPARATOR . 'dependency.json';
    $f.=DIRECTORY_SEPARATOR . 'dependency.php';
    if (!is_file($f)) return [];
    $waxed = $this->base;
    if (!defined("NODE")) define("NODE",$this->getNodeJSPath());
    if (!defined("DESIGN")) define("DESIGN", $this->base->design->getDesignPath());
    $a = include($f);
    //file_put_contents($json, json_encode($a));
    return $a;
  }

  /**
  * get_inside
  *
  * @param string $name
  * @return array<mixed>
  */
  public function get_inside(string $name): array {
    $f = $this->packagePath.$name;
    if (!is_dir($f)) return [];
    //$json = $f . DIRECTORY_SEPARATOR . 'inside.json';
    $f.= DIRECTORY_SEPARATOR . 'inside.php';

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
  * @param string $name
  * @return ?object
  */
  public function get_setter(string $name): ?object {
    $f = $this->packagePath.$name;
    if (!is_dir($f)) return [];
    //$json = $f . DIRECTORY_SEPARATOR . 'inside.json';
    $f.= DIRECTORY_SEPARATOR . 'setter.php';

    if (!is_file($f)) return null;
    include_once($f);
    $className = '\\Waxedphp\\Waxedphp\\php\\Setters\\' . $name . '\\Setter';
    return new $className($this->base);
  }


  /**
  * has plugin
  *
  * @param string $name
  * @return bool
  */
  public function has(string $name): bool {
    if (!empty($this->get_dependency($name))) return true;
    return false;
  }



}
