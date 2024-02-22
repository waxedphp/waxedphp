<?php
namespace Waxedphp\Waxedphp\php;

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


}

