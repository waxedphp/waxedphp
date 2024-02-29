<?php
namespace Waxedphp\Waxedphp;

class Reaction {

  /**
   * @var Base $base
   */
  private Base $base;

  /**
   * @var array<mixed> $reactions
   */
  private $reactions = array();

  /**
   * @var ?string $picked
   */
  private $picked = null;

  /**
   * @var array<mixed> $_chunks
   */
  private $_chunks = [];

  /**
  * constructor
  *
  * @param Base $base
  */
  public function __construct(Base $base){
    $this->base = &$base;
  }

  /**
  * get base
  *
  * @return \Waxedphp\Waxedphp\Base
  */
  public function getBase(): \Waxedphp\Waxedphp\Base {
    return $this->base;
  }

  /**
  * get plugin name
  *
  * @return string
  */
  private function getPluginName(): string {
    return $this->base->getPluginName();
  }

  /**
  * get defaults
  *
  * @return array<mixed>
  */
  private function getDefaults(): array {
    return $this->base->getDefaults();
  }

  /**
  * count reactions
  *
  * @return int
  */
  private function countReactions(): int {
    return count($this->reactions);
  }

  /**
  * get reactions
  *
  * @return array<mixed>
  */
  private function getReactions(): array {
    $a = [];$re = $this->reactions;
    foreach ($re as $k => $v) {
      if (is_array($v)) {
        $a[] = $v;
      } else if ((is_object($v))&&(is_callable([$v, 'toArray']))) {
        $a[] = $v->toArray();
      }
    }
    return $a;
  }

  /**
  * reset reactions
  *
  * @return object
  */
  private function resetReactions(): object {
    $this->reactions = [];
    return $this;
  }

  /**
  * append reactions
  *
  * @param array<mixed>|object $o
  * @return object
  */
  private function appendReactions(array|object $o): object {
    $this->reactions[] = $o;
    return $this;
  }

  /**
  * prepend reactions
  *
  * @param array<mixed>|object $o
  * @return object
  */
  private function prependReactions(array|object $o): object {
    if (count($this->reactions)>0) {
      array_unshift($this->reactions, []);
    };
    $this->reactions[0] = $o;
    return $this;
  }

  /**
  * str_last_replace
  *
  * @param string $search
  * @param string $replace
  * @param string $subject
  * @return string
  */
  private static function str_last_replace(string $search, string $replace, string $subject): string {
    $pos = strrpos($subject, $search);
    if($pos !== false) {
      $subject = substr_replace($subject, $replace, $pos, strlen($search));
    };
    return $subject;
  }

  /**
  * pick
  *
  * @param string $id
  * @return object
  */
  public function pick(string $id){
    $this->picked = $id;
    return $this;
  }

  /**
  * Main functionality. Displays template on page...
  *
  * @param array<mixed> $RECORD data to give inside template.
  * @param ?string $template html template filename, without extension .html .
  * @param int $onTime
  * @param ?string $whereId optional DOM id of alternative component, where template should appear.
  * @param bool $append optional - if template should be appended in the end of component.
  * @return object
  */
  function display(array $RECORD, ?string $template = null, ?int $onTime = 0, ?string $whereId = null, bool $append = false): object  {
    $a = new Reactions\Display($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($RECORD, $template, $onTime, $whereId, $append);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * Main functionality. Displays template on page, appending.
  *
  * @param array<mixed> $RECORD data to give inside template.
  * @param ?string $template html template filename, without extension .html .
  * @param int $onTime
  * @param ?string $whereId optional DOM id of alternative component, where template should appear.
  * @return object
  */
  function append(array $RECORD, ?string $template = null, ?int $onTime = 0, ?string $whereId = null): object  {
    $a = new Reactions\Append($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($RECORD, $template, $onTime, $whereId);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * Main functionality. Shows template in DIALOG window...
  *
  * @param array<mixed> $RECORD data to give inside template.
  * @param ?string $template html template filename, without extension .html .
  * @param int $timeout optional delay before action.
  * @param ?string $class optional css class added to dialog frame component.
  * @param bool $modal optional - if dialog should be displayed as modal.
  * @param string $signature
  * @return object
  */
  function dialog(array $RECORD, string $template = null, int $timeout=0, ?string $class = null, bool $modal = false, string $signature = 'modal'): object {
    $a = new Reactions\Dialog($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($RECORD, $template, $timeout, $class, $modal, $signature);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * Dialog window can be closed...
  *
  * @param string $signature
  * @param int $onTime optional delay before action.
  * @return object
  */
  function dialogClose(string $signature, int $onTime = 0): object {
    $a = new Reactions\DialogClose($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($signature, $onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * update
  *
  * @param array<mixed> $RECORD
  * @param int $timeout
  * @return object
  */
  function update(array $RECORD, int $timeout = 0): object {
    $a = new Reactions\Inspire($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($RECORD, $timeout);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * inspire
  *
  * @param array<mixed> $RECORD
  * @param int $timeout
  * @return object
  */
  function inspire(array $RECORD, int $timeout = 0): object {
    return $this->update($RECORD, $timeout);
  }

  /**
  * it is possible to INVALIDATE template...
  * For example, if user submited wrong data.
  * With this functionality,
  * its not needed to do duplicated and unsafe
  * browser-side validation anymore.
  *
  * This php method could take Exception,
  * (CrudflowException or PerkyException)
  * and utilize it to display on form template, what is wrong/missing...
  *
  * @param object|array<mixed> $inputs
  * @param ?string $template
  * @param ?string $whereId
  * @return object
  */
  function invalidate(array|object $inputs = [], ?string $template = null, ?string $whereId = null): object  {
    $a = new Reactions\Invalidate($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($inputs, $template, $whereId);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * it is possible to SHOW elements...
  *
  * @param string $whereId DOM id of component, which should be shown.
  * @param int $onTime
  * @return object
  */
  function show(?string $whereId = null, int $onTime = 0): object  {
    $a = new Reactions\Show($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($whereId, $onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * it is possible to HIDE elements...
  *
  * @param string $whereId DOM id of component, which should be hidden.
  * @param int $onTime
  * @return object
  */
  function hide(?string $whereId = null, int $onTime = 0): object  {
    $a = new Reactions\Hide($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($whereId, $onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * it is possible to install some new behaviors to browser side
  * javascript instance of jam...
  *
  * @param array<mixed> $actions
  * @return object
  */
  function behave($actions): object  {
    $a = new Reactions\Behave($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($actions);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * pack behaviors
  *
  * @param  $a
  * @return
  function packBehaviors($a) {
    $b = array();
    foreach ($a as $k => $v) {
      $b[$k] = '(function(){return function(o) {' . $v . '}})()';
    };
    return $b;
  }
  */

  /**
  * it is possible to set hash part of url in browser...
  *
  * @param string $hash
  * @param int $onTime
  * @return object
  */
  function hashState(string $hash, int $onTime = 0): object  {
    $a = new Reactions\HashState($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($hash, $onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * load
  *
  * @param ?string $action
  * @param array<mixed> $data
  * @param ?string $url
  * @param int $onTime
  * @return object
  */
  function load(string $action = null, array $data = [], string $url = null, int $onTime = 0): object {
    $a = new Reactions\Load($this->getBase());
    if ($this->picked) {
      //$a->pick($this->picked);
    };
    $a->configure($action, $data, $url, $onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * we could trigger ajax request to  url...
  * Altough this is intended as first step of jam intercourse with server,
  * invocated by fresh loaded page,
  * it could be called also later, from server.
  * In such case will be probably usefull parameter "$onTime",
  * in this manner could be simply established "server polling", every 10 seconds or so.
  *
  * @param  $url
  * @param  $data
  * @param  $onTime
  * @return

  function load1($url, $data = false, $onTime = false){
    $a = array(
      'action' => 'load',
      'url' => $url,
    );
    if(is_array($data)){
      $a['data'] = $data;
    };
    if($onTime>0){
      $a['ontime']=intval($onTime);
    };
    if ($this->picked) {
      $a['pick'] = '#'.$this->picked;
    };
    $this->reactions[] = $a;
    return $this;
  }
  */

  /**
  * we could reload page in consequence of ajax calling...
  *
  * @param int $onTime optional delay before action.
  * @return object
  */
  function reload(int $onTime = 0): object {
    $a = new Reactions\Reload($this->getBase());
    $a->configure($onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * we could scroll page in consequence of ajax calling...
  *
  * @param int $speed optional delay before action.
  * @return object
  */
  function scrollTop(int $speed = 0): object {
    $a = new Reactions\ScrollTop($this->getBase());
    $a->configure($speed);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * we could redirect browser in consequence of ajax calling...
  *
  * @param string $url URL where to redirect.
  * @param int $onTime
  * @return object
  */
  function redirect(string $url, int $onTime = 0): object {
    $a = new Reactions\Redirect($this->getBase());
    $a->configure($url, $onTime);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * title
  *
  * @param string $title
  * @return object
  */
  function title(string $title): object {
    $a = new Reactions\Title($this->getBase());
    $a->configure($title);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * it is possible to PLUG, load new jam plugins from server on demand...
  *
  * @param string|array<mixed> $plug
  * @return object
  */
  function loadPlugins(string|array $plug): object {
    $a = new Reactions\LoadPlugins($this->getBase());
    $a->configure($plug);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * it is possible to PLUG, load new jam plugins from server on demand...
  *
  * @param string $name
  * @param string $html
  * @return object
  */
  function loadTemplate(string $name, string $html): object {
    $a = new Reactions\LoadTemplate($this->getBase());
    $a->configure($name, $html);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * send raw
  *
  * @param array<mixed> $parameters
  * @return object
  */
  function sendRaw(array $parameters){
    $a = new Reactions\SendRaw($this->getBase());
    if ($this->picked) {
      $a->pick($this->picked);
    };
    $a->configure($parameters);
    $this->appendReactions($a);
    return $this;
  }

  /**
  * view
  *
  * @param string $id
  * @param string|array<mixed> $template
  * @param array<mixed> $data
  * @param ?array<string> $js
  * @return string
  */
  public function view(string $id, string|array $template, array $data = [], $js = null): string {
    $data = array_merge($this->getDefaults(), $data);
    $t = '';
    if (is_array($template)){
      if (isset($template['htmltext'])) $t = $template['htmltext'];
    } else if (is_string($template)) {
      $t = $this->base->design->mode('html')->route(explode('/',$template))->GET();
    };
    $a = new Reactions\Init($this->getBase());
    $this->prependReactions($a);
    $setup = array(
      'action' => 'multi',
      'actions' => $this->getReactions(),
    );
    $s = '';
    $s .= $this->base->plugin->JS() . "\n";
    if (is_array($js)) {
      foreach ($js as $script) {
        $s .= '<script type="text/javascript" src="'.$script.'" ></script>'."\n";
      };
    }
    $s .= '<script type="text/javascript">$( "#' . $id . '" ).' . $this->getPluginName() . '(' . json_encode($setup) . ');</script>';
    $mustache=new Mustache($this->getBase());
    $t = $mustache->render($t, $data);
    $t = self::str_last_replace('<head>', '<head>' . $this->base->design->BASE(), $t);
    $t = self::str_last_replace('</head>', $this->base->plugin->CSS() . '</head>', $t);
    if ((is_array($template))&&(isset($template['csstext']))) {
      $t = self::str_last_replace('</head>', "\n".'  <style>' ."\n".$template['csstext']."\n".'  </style>'."\n".'</head>', $t);
    };
    $t = self::str_last_replace('</body>', $s . '</body>', $t);
    return $t;
  }

  /**
  * response
  *
  * @return string
  */
  function response(): string {
    if ($this->base->getChunked()) {
      $this->chunk();
      return '';
    };
    //$this->throwHeaders();
    $a = json_encode(array(
      'action' => 'multi',
      'actions' => $this->getReactions(),
    ), JSON_UNESCAPED_UNICODE);
    $this->resetReactions();
    return ($a) ? $a : '';
  }

  /**
  * responseArray
  *
  * @return array<mixed>
  */
  function responseArray(): array {
    if ($this->base->getChunked()) {
      $this->chunk();
      return '';
    };
    //$this->throwHeaders();
    $a = [
      'action' => 'multi',
      'actions' => $this->getReactions(),
    ];
    $this->resetReactions();
    return ($a) ? $a : [];
  }

  /**
  * Final method throws ajax response.
  * OK
  *
  * @return object
  */
  function chunk(): object {
    if ($this->countReactions()<1) {
      return $this;
    };
    $reactions = $this->getReactions();
    $json = json_encode(array(
      'action' => 'multi',
      'actions' => $reactions,
    ), JSON_UNESCAPED_UNICODE);
    if (!$json) $json = json_encode([
      'error' => json_last_error(),
      'message' => json_last_error_msg(),
    ]);
    if (count($this->_chunks) == 0) {
      @ini_set('zlib.output_compression', 0);
      @ini_set('implicit_flush', 0);
      while (ob_get_level() > 0) {
        @ob_end_clean();
      };
      set_time_limit(0);
      $this->throwHeaders();
    };
    //ob_end_flush();
    if (ob_get_level() == 0) ob_start();
    $padstr = "" . "\n\n";//str_pad("", 1024, " ");
    //echo('<chunk>' . $json . $padstr . '</chunk>');
    echo($json . $padstr);
    ob_flush();
    flush();
    //ob_start();
    $this->_chunks[] = $reactions;
    $this->resetReactions();
    return $this;
  }

  /**
  * flush autocomplete
  *
  * @param array<mixed> $RECORD
  * @param string $type
  * @return void
  */
  function flushAutocomplete(array $RECORD, string $type = 'suggestions') {
    $b = array();
    switch ($type) {
      case 'neo':
        foreach ($RECORD as $row) {
          $b[] = array(
            'label' => $row['sTITLE'],
            'id' => $row['iID'],
          );
        };
      break;
      case 'tagit':
        foreach ($RECORD as $row) {
          $b[] = $row['sTITLE'];
        };
      break;
      case 'suggestions':
        foreach ($RECORD as $row) {
          $b[] = array(
            'value' => $row['sTITLE'],
            'data' => $row['iID'],
          );
        };
        $b = array('suggestions' => $b,);
      break;
      default:
        foreach ($RECORD as $row) {
          $b[] = array(
            'title' => $row['sTITLE'],
            'id' => $row['iID'],
          );
        };
      break;
    }
    $this->throwHeaders();
    die(json_encode($b));
  }

  /**
  * flush raw
  *
  * @param array<mixed> $b
  * @return void
  */
  function flushRaw(array $b): void {
    $this->throwHeaders();
    die(json_encode($b));
  }

  /**
  * throw headers
  *
  * @return void
  */
  function throwHeaders() {
    if ($this->base->getChunked()) {
      header('Content-Type: text/plain; charset=utf8');
      //header('Transfer-Encoding: chunked');
      header('X-Content-Type-Options: nosniff');
    } else {
      header('Content-Type: application/json; charset=utf8');
    }
    if ($this->base->getCrosssite()) {
      header('Access-Control-Allow-Origin: ' . $this->base->getCrosssite());
    };
  }

  /**
  * Final method throws ajax response.
  *
  * @return object
  */
  function flush(): object {
    if ($this->base->getChunked()) {
      return $this->chunk();
    };
    $this->throwHeaders();
    die(json_encode(array(
      'action' => 'multi',
      'actions' => $this->getReactions(),
    ), JSON_UNESCAPED_UNICODE));
  }

}

