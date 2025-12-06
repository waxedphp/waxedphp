<?php
namespace Waxedphp\Waxedphp\Php;

class Mustache {

  /**
   * @var Base $base
   */
  private Base $base;

  /**
   * @var \Mustache_Engine $mustache
   */
  private \Mustache_Engine $mustache;

  /**
  * constructor
  *
  * @param  $base
  */
  function __construct(Base $base) {
    $this->base = &$base;
    $this->mustache = new \Mustache_Engine(array(
        'template_class_prefix' => '__MyTemplates_',
        //'cache' => WRITEPATH .'cache' . DIRECTORY_SEPARATOR . 'mustache',
        //'cache_file_mode' => 0644, // Please, configure your umask instead of doing this :)
        //'cache_lambda_templates' => true,
        //'loader' => new Mustache_Loader_FilesystemLoader(APPPATH .'views', array('extension' => '.html',)),
        'loader' => new \Mustache_Loader_StringLoader(),
        'partials_loader' => new \Mustache_Loader_FilesystemLoader(
          $this->base->design->getDesignPath(), ['extension' => '.html',]
        ),
        'helpers' => array(
          'i18n' => function($text = '') {
            // do something translatey here...
          },
          'vardump' => function($obj = []) {
            return '<xmp>' . print_r($obj, true) . '</xmp>';
          },
          'path' => function($arr = []) {
            $s = '';
            foreach ($arr as $word) {
              if ($s) $s .= '/';
              $s .= $word;
            };
            return $s;
          },
        ),
        'escape' => function($value = '') {
          if (is_string($value)) {
            return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
          } else if (is_integer($value)) {
            return intval($value);
          } else if (is_float($value)) {
            return htmlspecialchars((String)$value, ENT_COMPAT, 'UTF-8');
          } else if (is_bool($value)) {
            return $value?'TRUE':'FALSE';
          } else {
            return '[' . gettype($value) . ']';
          }
        },
        'charset' => 'UTF-8',
        'logger' => new \Mustache_Logger_StreamLogger('php://stderr'),
        'strict_callables' => true,
        'pragmas' => [\Mustache_Engine::PRAGMA_FILTERS],

    ));
  }

  /**
  * render
  *
  * @param string $template
  * @param array<mixed> $vars
  * @return string
  */
  function render(string $template, array $vars = array()) {
    $tpl = $this->mustache->loadTemplate($template); // loads __DIR__.'/views/foo.mustache';
    return $tpl->render($vars);
  }

  /**
  * renderer
  *
  * @param string $template
  * @return mixed
  */
  function renderer(string $template) {
    return $this->mustache->loadTemplate($template); // loads __DIR__.'/views/foo.mustache';
  }
  
  function get() {
    return $this->mustache;
  }
  
  private function clearArrayRecursively(array &$arr) {
    $a = [];
    foreach ($arr as $row) {
      if ((isset($row['type'])) && (in_array($row['type'], ['#', '_v']))) {
        if (($row['type']=='#') && (isset($row['nodes']))) {
          $this->clearArrayRecursively($row['nodes']);
        }
        $a[] = $row;
      }
    }
    $arr = $a;
  }
  
  private function reSort($nodes, &$heap, &$lastRoot) {
      foreach ($nodes as $row) {
        if ($row['type'] == '_v') {
          $a = explode('.', $row['name']);
          $root = &$lastRoot;
          foreach ($a as $n) {
            if (!isset($root[$n])) $root[$n] = [];
            $root = &$root[$n];
          }
        }
        if ($row['type'] == '#') {
          $a = explode('.', $row['name']);
          $root = &$lastRoot;
          $i = 0;
          foreach ($a as $n) {
            $i++;
            if (!isset($root[$n])) $root[$n] = [];
            $root = &$root[$n];
            if ($i === count($a)) {
              if (!isset($root[0])) $root[0] = [];
              $root = &$root[0];
              $this->reSort($row['nodes'], $heap, $root);
            }
          }
        }        
      };
  }
  
  public function getTree(string $code): array {
  
      $mu = $this->mustache;
      $a = $mu->getTokenizer()->scan($code);
      
      $pa = $mu->getParser();
      //$pa->setOptions($mu->getOptions());
      $pa->setPragmas($mu->getPragmas());

      $b = $pa->parse($a);
      $this->clearArrayRecursively($b);
      
      $c = [];
      $this->reSort($b, $c, $c);
      
      
      return $c;
  }

}

