<?php
namespace Waxedphp\Waxedphp;

//require "Functions.php";
/**
 * library maintaining ajax communication.
 *
 */
class Base {

  /**
   * @var string $pluginName
   */
  private $pluginName = 'waxxx';

  /**
   * @var ?string $ajaxUrl
   */
  private ?string $ajaxUrl = null;

  /**
   * @var string $engine
   */
  private $engine = 'mark2';

  /**
   * @var ?string $baseDir
   */
  private ?string $baseDir = null;

  /**
   * @var ?string $appPath
   */
  private ?string $appPath = null;

  /**
   * @var ?string $_route
   */
  private ?string $_route = null;

  /**
   * @var array<mixed> $defaults
   */
  private $defaults = [];

  /**
   * @var bool $chunked
   */
  private bool $chunked = false;

  /**
   * @var bool $crosssite
   */
  private bool $crosssite = false;

  /**
   * @var bool $development
   */
  private static $development = false;

  /**
   * @var Plugin $plugin
   */
  public Plugin $plugin;

  /**
   * @var Design $design
   */
  public Design $design;

  /**
   * @var Action $action
   */
  public Action $action;

  /**
   * @var Reaction $action
   */
  public Reaction $reaction;

  /**
   * @var Vocab $vocab
   */
  public Vocab $vocab;

  /**
   * @var Utils $utils
   */
  public Utils $utils;

  /**
  * constructor
  *
  */
  public function __construct() {
    $bd = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
    $this->baseDir = ($bd)?$bd:null;
    $ap = realpath($this->baseDir . DIRECTORY_SEPARATOR . '..');
    $this->appPath = ($ap)?$ap:null;

    $this->plugin = new Plugin($this);
    $this->design = new Design($this);
    $this->action = new Action($this);
    $this->reaction = new Reaction($this);
    $this->vocab = new Vocab($this);
    $this->utils = new Utils($this);
  }

  /**
  * setup
  *
  * @param array<mixed> $cfg
  * @return object
  */
  public function setup(array $cfg): object {
    if ((isset($cfg['development'])) && (is_bool($cfg['development']))) {
      $this->setDevelopment($cfg['development']);
    };
    if ((isset($cfg['development'])) && (is_string($cfg['development']))) {
      $this->setDevelopment($cfg['development']);
    };
    if ((isset($cfg['writable_path'])) && (is_string($cfg['writable_path']))) {
      $this->plugin->setWritablePath($cfg['writable_path']);
    };
    if ((isset($cfg['freezed'])) && (is_bool($cfg['freezed']))) {
      //??? $this->freezed = $cfg['freezed'];
    };
    if ((isset($cfg['apified'])) && (is_bool($cfg['apified']))) {
      //$this->apified = $cfg['apified'];
    };
    if ((isset($cfg['chunked'])) && (is_bool($cfg['chunked']))) {
      $this->chunked = $cfg['chunked'];
    };
    if ((isset($cfg['engine'])) && (is_string($cfg['engine']))) {
      $this->engine = $cfg['engine'];
    };
    if ((isset($cfg['plugin_name'])) && (is_string($cfg['plugin_name']))) {
      $this->pluginName = $cfg['plugin_name'];
    };
    if ((isset($cfg['design_route'])) && (is_string($cfg['design_route']))) {
      $this->setDesignRoute($cfg['design_route']);
    };
    if ((isset($cfg['plugin_route'])) && (is_string($cfg['plugin_route']))) {
      $this->setPluginRoute($cfg['plugin_route']);
    };
    if ((isset($cfg['action_route'])) && (is_string($cfg['action_route']))) {
      $this->setActionRoute($cfg['action_route']);
    };
    if ((isset($cfg['design_path'])) && (is_string($cfg['design_path']))) {
      $this->setDesignPath($cfg['design_path']);
    };
    if ((isset($cfg['package_path'])) && (is_string($cfg['package_path']))) {
      //$this->setPackagePath($cfg['package_path']);
    };
    if ((isset($cfg['action_path'])) && (is_string($cfg['action_path']))) {
      //$this->setActionPath($cfg['action_path']);
    };
    if ((isset($cfg['nodejs_path'])) && (is_string($cfg['nodejs_path']))) {
      $this->setNodeJsPath($cfg['nodejs_path']);
    };
    if ((isset($cfg['action_prefix'])) && (is_string($cfg['action_prefix']))) {
      $this->setActionPrefix($cfg['action_prefix']);
    };
    if ((isset($cfg['defaults'])) && (is_array($cfg['defaults']))) {
      $this->setDefaults($cfg['defaults']);
    };


    return $this;
  }

  /**
  * route
  *
  * @param string $name
  * @return object
  */
  public function route(String $name): object {
    $this->_route = $name;
    return $this;
  }

  /**
  * configure
  *
  * @param array<mixed> $cfg
  * @param string $name
  * @return object
  */
  public function configure(array $cfg, string $name = 'waxed'): object {
    if (!isset($cfg[$name])) return $this;
    if (is_array($cfg[$name])) $this->setup($cfg[$name]);
    return $this;
  }

  /**
  * set vocab
  *
  * @param string $name
  * @param string $lang
  * @return object
  */
  public function setVocab(string $name, string $lang): object {
    $this->vocab->load($name)->lang($lang);
    return $this;
  }

  /**
  * set chunked
  *
  * @param bool $chunked
  * @return object
  */
  public function setChunked(bool $chunked): object {
    $this->chunked = $chunked;
    return $this;
  }

  /**
  * get chunked
  *
  * @return bool
  */
  public function getChunked(): bool {
    return $this->chunked;
  }

  /**
  * get crosssite
  *
  * @return bool
  */
  public function getCrosssite(): bool {
    return $this->crosssite;
  }

  /**
  * replace variables in template
  *
  * @param string $template
  * @param array<mixed> $variables
  * @return ?string
  */
  public function replaceVariablesInTemplate(string $template, array $variables): ?string {
    return preg_replace_callback('#{(.*?)}#',
      function($match) use ($variables){
              $match[1] = trim($match[1], '$');
              if (isset($variables[$match[1]])) {
                return $variables[$match[1]];
              } else {
                return '';
              }
      },
      $template
    );
  }

  /**
  * method setTemplateBase
  * template base is url, from where are templates loaded.
  * Instead of defining each time full url to template,
  * we can define base url here, and later call only file-name.
  *
  * @param string $route
  * @return object
  */
  private function setDesignRoute(string $route): object {
    if (isset($this->_route)) {
      $route = static::replaceVariablesInTemplate($route, [
        'ROUTE' => $this->_route,
      ]);
    }
    $this->design->setDesignRoute($route);
    return $this;
  }

  /**
  * method setPluginRoute
  *
  * @param string $route
  * @return object
  */
  private function setPluginRoute(string $route): object {
    if (isset($this->_route)) {
      $route = static::replaceVariablesInTemplate($route, [
        'ROUTE' => $this->_route,
      ]);
    }
    $this->plugin->setPluginRoute($route);
    return $this;
  }

  /**
  * method setActionRoute
  *
  * @param string $route
  * @return object
  */
  public function setActionRoute(string $route): object {
    if (isset($this->_route)) {
      $route = static::replaceVariablesInTemplate($route, [
        'ROUTE' => $this->_route,
      ]);
    }
    $this->ajaxUrl = $route;
    $this->setDefaults(['ajax' => $this->ajaxUrl]);
    return $this;
  }

  /**
  * method setDesignPath
  *
  * @param string $s
  * @return object
  */
  public function setDesignPath(string $s): object {
    $this->design->setDesignPath($s);
    return $this;
  }

  /**
  * method setNodeJsPath
  *
  * @param string $s
  * @return object
  */
  private function setNodeJsPath(string $s): object {
    $this->plugin->setNodeJsPath($s);
    return $this;
  }

  /**
  * method setActionPrefix
  *
  * @param string $s
  * @return object
  */
  private function setActionPrefix(string $s): object {
    $this->action->setPrefix($s);
    return $this;
  }

  /**
  * set development
  *
  * @param bool|string $b
  * @return object
  */
  private function setDevelopment(bool|string $b): object {
    if  ((is_string($b))&&($b === 'true')) {
      self::$development = true;
    } else if (is_bool($b)) {
      self::$development = $b;
    }
    if (self::$development) {
      $this->plugin = new PluginDev($this);
    }
    return $this;
  }

  /**
  * is_development
  *
  * @return bool
  */
  public function is_development(): bool {
    return self::$development;
  }

  /**
  * method setDefaults
  *
  * @param array<mixed> $a
  * @return object
  */
  public function setDefaults(array $a): object {
    $this->defaults = array_merge($this->defaults, $a);
    return $this;
  }

  /**
  * get defaults
  *
  * @return array<mixed>
  */
  public function getDefaults(): array {
    return $this->defaults;
  }

  /**
  * Returns canonical ajax url, defaults to './ajax'
  *
  * @return ?string
  */
  function getAjaxUrl(): ?string {
    return $this->ajaxUrl;
  }

  /**
  * set engine
  *
  * @param string $engine
  * @return object
  */
  function setEngine(string $engine): object {
    $this->engine = $engine;
    return $this;
  }

  /**
  * get engine
  *
  * @return string
  */
  function getEngine(): string {
    return $this->engine;
  }

  /**
  * get plugin name
  *
  * @return string
  */
  public function getPluginName():string {
    return $this->pluginName;
  }

  /**
  * get app path
  *
  * @return ?string
  */
  function getAppPath(): ?string {
    return $this->appPath;
  }

  /**
  * design
  *
  * @param  $design
  * @return
  function design($design) {
  }
  */

  /**
  * js
  *
  * @param  $listing
  * @return
  public function JS($listing) {
    return '<xmp>' . print_r($this->plugin->read($listing), true) . '</xmp>';
  }
  */

  /**
  * css
  *
  * @param  $listing
  * @return
  public function CSS($listing) {
    return $this->plugin->read($listing);
  }
  */

  /**
  * html
  *
  * @param  $listing
  * @return
  public function HTML($listing) {
    //return $this->dependencies->load_html($listing);
  }
  */

  /**
  * setter
  *
  * @param string $name
  * @return ?object
  */
  function setter(string $name): ?object {
      switch($name){
        case 'AceEditor':
          return new \Waxedphp\Waxedphp\Setters\AceEditor($this);
        case 'Asteroids':
          return new \Waxedphp\Waxedphp\Setters\Asteroids($this);
        case 'Tabulator':
          return new \Waxedphp\Waxedphp\Setters\Tabulator($this);
        //case 'SlimSelect':
          //return new \Waxedphp\Waxedphp\Setters\SlimSelect($this);
        case 'PieChart':
          return new \Waxedphp\Waxedphp\Setters\PieChart($this);
        case 'TimeChart':
          return new \Waxedphp\Waxedphp\Setters\TimeChart($this);
        case 'Behaviors':
          return new \Waxedphp\Waxedphp\Setters\Behaviors($this);
        case 'Paging':
          return new \Waxedphp\Waxedphp\Setters\Paging($this);
        case 'Dropzone':
          return new \Waxedphp\Waxedphp\Setters\Dropzone($this);
      }
      return $this->plugin->get_setter(strtolower($name));
      //return null;
  }

  /**
  * __call
  *
  * @param string $name
  * @param array<mixed> $arguments
  * @return mixed
  */
  public function __call(string $name, array $arguments): mixed {
    $cal = [$this->reaction,$name];
    $availableReturning = ['view', 'response', 'responseArray'];
    if (in_array($name, $availableReturning)) {

      if (!is_callable($cal)) return [];
      return call_user_func_array($cal, $arguments);
    }
    $availableReactions = ['display','append','dialog','dialogClose',
    'invalidate', 'update', 'inspire',
    'load', 'reload', 'redirect', 'scrollTop',
    'show', 'hide', 'title', 'behave', 'sendRaw',
    'loadTemplate', 'loadPlugins', 'hashState'];
    if (in_array($name, $availableReactions)) {
      if (!is_callable($cal)) return $this;
      call_user_func_array($cal, $arguments);
      return $this;
    }
    $availableVoid = [
    'flush','flushRaw','flushAutocomplete',
    'chunk', 'pick'];
    if (in_array($name, $availableVoid)) {
      if (!is_callable($cal)) return $this;
      call_user_func_array($cal, $arguments);
      return $this;
    }
    return $this;
  }

}

