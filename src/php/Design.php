<?php
namespace JasterStary\Waxed\php;

class Design {

  /**
   * @var Base $base
   */
  private Base $base;
  /**
   * @var string $defaultTemplateDialog
   */
  private string $defaultTemplateDialog = 'dialog';
  /**
   * @var string $path
   */
  private string $path;
  /**
   * @var string $_style
   */
  private string $_style = 'light';
  /**
   * @var string $_base
   */
  private string $_base = '';
  /**
   * @var ?string $mode
   */
  private ?string $mode = null;
  /**
   * @var array<string> $aroute
   */
  private array $aroute = [];
  /**
   * @var string $aroute
   */
  private string $route = '';
  /**
  * constructor
  *
  * @param  $base
  */
  public function __construct(Base $base){
    $this->base = $base;
    //$this->designPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
    $this->path = $this->base->getAppPath() . 'views';
  }

  /**
  * set base
  *
  * @param string $route
  * @return object
  */
  public function setBase(string $route): object {
    $this->_base = $route;
    return $this;
  }

  /**
  * set style
  *
  * @param string $style
  * @return object
  */
  public function setStyle(string $style): object {
    $this->_style = $style;
    return $this;
  }

  /**
  * get style
  *
  * @return string
  */
  public function getStyle(): string {
    return $this->_style;
  }

  /**
  * get style query
  *
  * @return string
  */
  public function getStyleQuery(): string {
    return '&style='.$this->_style;
  }

  /**
  * mode
  *
  * @param string $mode
  * @return Design
  */
  public function mode(string $mode): Design {
    $this->mode = $mode;
    return $this;
  }

  /**
  * route
  *
  * @param array<string> $aroute
  * @return Design
  */
  public function route(array $aroute): Design {
    $this->aroute = $aroute;
    return $this;
  }

  /**
  * set design route
  *
  * @param string $route
  * @return object
  */
  public function setDesignRoute(string $route): object {
    $this->route = $route;
    return $this;
  }

  /**
  * get design route
  *
  * @return string
  */
  public function getDesignRoute(): string {
    return $this->route;
  }

  /**
  * set design path
  *
  * @param string $path
  * @return object
  */
  public function setDesignPath(string $path): object {
    $path = realpath(getcwd() . DIRECTORY_SEPARATOR . $path);
    if (!$path) {
      throw new \Exception('Design path doesnt exists.');
    }
    $this->path = $path;
    return $this;
  }

  /**
  * get design path
  *
  * @return string
  */
  public function getDesignPath(): string {
    return $this->path;
  }

  /**
  * get route
  *
  * @param ?string $templateName
  * @return string
  */
  public function getRoute(?string $templateName = null): string {
    if (!$this->route) {
      throw new \Exception('Design route is undefined.');
    };
    if(!$templateName){
      $templateName = $this->defaultTemplateDialog;
    } else if (strpos($templateName, '/') === 0) {
      return dirname($this->route) . $templateName;
    };
    return $this->route . $templateName;
  }

  /**
  * dispatch
  *
  * @param string $url
  * @return void
  */
  public function dispatch(string $url): void {
    if (!$this->route) {
      echo '';
      return;
    };
    $url = str_replace(ltrim($this->route, '/'), '', $url);

    $ext_pos = strrpos($url, '.');
    if (!$ext_pos) {
      echo '';
      return;
    };
    $route = substr($url, 0, $ext_pos);
    $extension = substr($url, $ext_pos+1);

    $aRoute = explode('/', $route);

    $this->mode($extension)->route($aRoute);
    echo $this->GET();
  }

  /**
  * get
  *
  * @return string
  */
  public function GET(): string {
    switch ($this->mode) {
      case 'js':
        header("Content-type: application/javascript");
        return $this->load('js', $this->aroute);
      case 'css':
        header("Content-type: text/css");
        return $this->load('css', $this->aroute);
      case 'html':
        header("Content-type: text/html");
        return $this->loadHTML('html', $this->aroute);
      case 'png':
      case 'jpg':
      case 'jpeg':
      case 'gif':
        header("Content-type: image/" . $this->mode);
        return $this->load('image', $this->aroute);
      case 'ttf':
      case 'woff':
        header("Content-type: application/octet-stream");
        return $this->load('html', $this->aroute);
      case 'svg':
        header("Content-type: image/svg+xml");//text/svg
        return $this->load('svg', $this->aroute);
      case 'dbg':
        header("Content-type: text/plain");
        return print_r($this->aroute, true);
    }
    return '';
  }

  /**
  * error message
  *
  * @param string $message
  * @return string
  */
  protected function errorMessage(string $message): string {
    switch ($this->mode) {
      case 'js':
      case 'css':
      case 'dbg':
        return '/* ' . $message . ' */';
      case 'html':
        if ($this->base->is_development()) {
          return addslashes($message) . '<div class="jsonviewer" data-collapsed="false" ></div>';
        };
        return ' ' . $message . ' ';
      case 'png':
      case 'jpg':
      case 'jpeg':
      case 'gif':
        return '';
      case 'ttf':
      case 'woff':
        return '';
      case 'svg':
        return '';
    }
    return '';
  }

  /**
  * load
  *
  * @param string $type
  * @param array<string> $route
  * @return string
  */
  protected function load(string $type, array $route): string {
    $p = [$this->path];
    $p = array_merge($p, $route);
    $p = implode(DIRECTORY_SEPARATOR, $p);
    $p .= '.' . $this->mode;
    $pp = realpath($p);
    if (!$pp) $pp = '';
    if (substr($pp, 0, strlen($this->path)) != $this->path) {
      if ($this->base->is_development()) {
        return $this->errorMessage("Invalid path: " . $pp . " (".$this->path.")");
      } else {
        return $this->errorMessage("Invalid path!");
      }
    };
    if (file_exists($pp)) {
      return (String)file_get_contents($pp);
    };
    if ($this->base->is_development()) {
      return addslashes($p) . '<div class="jsonviewer" data-collapsed="false" ></div>';
    };
    return $this->errorMessage(addslashes($pp));
  }

  /**
  * load html
  *
  * @param string $type
  * @param array<string> $route
  * @return string
  */
  protected function loadHTML(string $type, array $route): string {
    $p = [$this->path];
    $p = array_merge($p, $route);
    $p = implode(DIRECTORY_SEPARATOR, $p);
    $p .= '.' . $this->mode;
    $pp = realpath($p);
    if (!$pp) $pp = '';
    if (substr($pp, 0, strlen($this->path)) != $this->path) {
      if ($this->base->is_development()) {
        return $this->errorMessage("Invalid path: " . $p . " (".$this->path.")");
      } else {
        return $this->errorMessage("Invalid path!");
      }
    };

    if (!$pp) {
      $pp = 'NULL';
    } else if (file_exists($pp)) {
      return (String)file_get_contents($pp);
    };

    if ($this->base->is_development()) {
      return addslashes($pp) . '<div class="jsonviewer" data-collapsed="false" ></div>';
    };

    return $this->errorMessage(addslashes($pp));
  }

  /**
  * base
  *
  * @return string
  */
  public function BASE() {
    if (!$this->_base) return '';
    return "\n".'<base href="'.$this->_base.'" />';
  }

}

