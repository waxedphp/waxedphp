<?php
namespace Waxedphp\Waxedphp\php;

class AndroidJs{

  private \Waxedphp\Waxedphp\php\Base $waxed;
  private array $behaviors = [];
  private string $projectRoot = '';

  private array $package = [
    "version" => "1.0.0",
    "name" => "apka",
    "app-name" => "apka",
    "package-name" => "apka",
    "project-type" => "webview",
    "icon" => "./assets/icon/icon.png",
    "dist-path" => "./dist",
    "permission" => [
      "android.permission.INTERNET"
    ],
    "description" => "",
    "main" => "main.js",
    "scripts" => [
      "start:dev" => "node .",
      "build" => "androidjs build"
    ],
    "author" => "",
    "license" => "MIT", //(ISC)
    "dependencies" => [
      "androidjs" => "^2.0.2"
    ],
    "project-name" => "apka",
    "theme" => [
      "fullScreen" => true
    ]
  ];
  /**
  * constructor
  *
  * @param Waxedphp\Waxedphp\php\Base $waxed
  */
  function __construct(\Waxedphp\Waxedphp\php\Base $waxed) {
    $this->waxed = $waxed;
    $this->waxed->setup([
      'design_route' => '',
    ]);
  }

  /**
  * set project root
  *
  * @param string $folder
  * @return object
  */
  function setProjectRoot(string $folder): object {
    $this->projectRoot = $folder;
    return $this;
  }

  /**
  * load behaviors
  *
  * @param string $fname
  * @return object
  */
  function loadBehaviors(string $fname): object {
    $a = array();
    if (file_exists($fname)) {
      $js = file_get_contents($fname);
      //$methods = preg_split("/\[[a-zA-Z0-9\/]+\]/", $js);
      $methods = preg_split("/\/\/!(\.)+/", $js);
      foreach ($methods as $method) {
        $m = array();
        preg_match("/\[([a-zA-Z0-9\/\_\-\.]+)\]/", $method, $m);
        //echo '<xmp>';print_r($m);echo('</xmp>');
        if (count($m)==2) {
          $fun = str_replace($m[0], '', $method);
          //$fun = str_replace('192.168.88.109:3000','127.0.0.1:30001',$fun);
          //$a[$m[1]] = '(function(){return function(o) {' . $fun . '}})()';
          $a[$m[1]] = 'return function(o) {' . $fun . '};';
        }
      }
      //echo '<xmp>';print_r($a);echo('</xmp>');
      //die();
    };
    if (count($a)) {
      $this->waxed->behave($a);
    };
    return $this;
  }

  /**
  * set package name
  *
  * @param string $s
  * @return object
  */
  function setPackageName(string $s): object {
    $this->package["package-name"] = $s;
    return $this;
  }

  /**
  * set icon
  *
  * @param string $s
  * @return object
  */
  function setIcon(string $s): object {
    $this->package["icon"] = $s;
    return $this;
  }

  /**
  * set version
  *
  * @param string $s
  * @return object
  */
  function setVersion(string $s): object {
    $this->package["version"] = $s;
    return $this;
  }

  /**
  * set author
  *
  * @param string $s
  * @return object
  */
  function setAuthor(string $s): object {
    $this->package["author"] = $s;
    return $this;
  }

  /**
  * set description
  *
  * @param string $s
  * @return object
  */
  function setDescription(string $s): object {
    $this->package["description"] = $s;
    return $this;
  }

  /**
  * set permissions
  *
  * @param array $a
  * @return object
  */
  function setPermissions(array $a): object {
    $b = [];
    foreach ($a as $k) {
      $b[$k] = 'android.permission.'.$k;
    };
    $this->package["permission"] = array_values($b);
    return $this;
  }

  /**
  * set output dir
  *
  * @param string $s
  * @return object
  */
  function setOutputDir(string $s): object {
    $this->package["dist-path"] = $s;
    return $this;
  }

  /**
  * build application
  *
  * @return
  */
  function buildApplication() {
    $this->waxed->setup([
      'design_route' => '',
    ]);

    $id='main';
    $this->mainTemplate='appka/index';
    $vars = [];
    $additionalScripts = [
      '../assets/script.js',
    ];

    $html = $this->waxed->view($id, $this->mainTemplate, $vars, $additionalScripts);
    $html2 = str_replace('src="/plugin/','src="../assets/plug/plug.js?',$html);
    $html2 = str_replace('href="/plugin/','href="../assets/plug/plug.css?',$html2);
    file_put_contents($this->projectRoot.'views/index.html', $html2);
    $json = json_encode($this->package, JSON_PRETTY_PRINT);
    file_put_contents($this->projectRoot.'package.json', $json);
    exec('mkdir -p "'.$this->projectRoot.'assets/appka/"');
    exec('cp ../assets/appka/*.html "'.$this->projectRoot.'assets/appka/"');
    exec('mkdir -p "'.$this->projectRoot.'assets/plug/"');
    $units = $this->waxed->plugin->getUnits();
    $js = $this->waxed->plugin->load('js', $units);
    file_put_contents($this->projectRoot.'assets/plug/plug.js', $js);
    $css = $this->waxed->plugin->load('css', $units);
    file_put_contents($this->projectRoot.'assets/plug/plug.css', $css);
    //exec('androidjs build --release');
    return $html;

    $beh = array();
    foreach ($this->behaviors as $behavior) {
      $this->waxed->loadBehaviors($beh, APPPATH . 'controllers/js/' . $behavior . '.js');
    };
    //$this->waxed->setCrossSite();

    foreach ($this->tplBlocks as $block) {
      $this->waxed->loadTemplateBlock($block);
    };

    $aaa = [
      'title' => $this->appTitle,
      'ajax' => './ajax',
      'main_controller' => $beh,
    ];

    $this->waxed->display(
      $aaa, $this->tplBlocks[0]
    );
    //$this->waxed->getIn('content1', $this->plugins, '', 1);

    $app = '';

    //$fs = $head->factorySetup();

    $path = $this->projectPath . '/www/';
    $replacements = array();
    foreach ($fs['CSS'] as $ss) {
      $r = $this->_curldown('localhost' . $ss['link']);
      $pi = pathinfo($ss['link']);
      $fn = 'css/' . md5(str_replace(array('~','_'), '', $pi['basename'])).'.css';
      $replacements[$ss['link']] = $fn;
      file_put_contents($path . $fn, $r);
    }
    foreach ($fs['JSS'] as $ss) {
      $r = $this->_curldown('localhost' . $ss['link']);
      $pi = pathinfo($ss['link']);
      $fn = 'js/' . md5(str_replace(array('~','_'), '', $pi['basename'])).'.js';
      $replacements[$ss['link']] = $fn;
      file_put_contents($path . $fn, $r);
    }
    $templates = '';
    foreach ($this->tplBlocks as $ss) {
      $r = $this->_curldown('localhost' . $this->tplPath . $ss);
      $pi = pathinfo($ss);
      $templates .= "
        <div id=\"" . $pi['filename'] . "\" >
          " . str_replace(array('{{{','}}}','{{','}}'), array('%%%','%%%','%%','%%'), $r) . "
        </div>
      ";
    }

    foreach ($this->_meta as $k=>$v){
      //echo $k;
      $head->set($k, $v);
    };

    $vars = [
      'app' => $app,
      'templates' => $templates,
    ];
    if (isset($this->version_id)) {
      $vars['version_id'] = $this->version_id;
    }

    $html = $this->waxed->view($id, $this->mainTemplate, $vars, false);
    file_put_contents($path . 'index.html', $html);
    return $html;

  }


}

