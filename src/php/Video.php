<?php
namespace JasterStary\Waxed\php;


class Video {
    
  public function __construct(){
    //$this->path = $base->getAppPath() . 'views';
    $this->path = getcwd() . DIRECTORY_SEPARATOR . 'video';
    $this->_headers = [];
    $this->_errors = [];
    $this->_body = null;
    $this->_sp = $_SERVER["SERVER_PROTOCOL"];
    $this->_dbg = 1;
  }
    
  public function setPath($path, $bRoot = false){
    if ($bRoot) {
      $this->path = $path;
    } else {
      $this->path = getcwd() . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
    };
    return $this;
  }

  public function getPath(){
    return $this->path;
  }
  
  public function setRoute($route){
    $this->route = $route;
    return $this;
  }

  public function getRoute(){
    return $this->route;
  }

  public function dispatch($url){
    $url = str_replace(ltrim($this->route, '/'), '', $url);
    $ext_pos = strrpos($url, '.');
    $uu = substr($url, 0, $ext_pos);
    $ee = substr($url, $ext_pos+1);

    $uu = explode('/', $uu);
    

    $this->setRequestedMode($ee)->setRequestedRoute($uu);
    
    $this->retrieve();
    //print_r($this->_headers);die('?');
    return $this;
  }

  public function setRequestedMode($mode){
    $this->mode = $mode;
    return $this;
  }
  
  public function setRequestedRoute($aroute){
    $this->aroute = $aroute;
    return $this;
  }

  private function checkRequestedPath() {
    $p = [$this->path];
    $p = array_merge($p, $this->aroute);
    $p = implode(DIRECTORY_SEPARATOR, $p);
    //$p .= '.' . $this->mode;
    $pp = realpath($p);
    if (substr($pp, 0, strlen($this->path)) != $this->path) {
      $this->_errors[] = "Invalid path.";
      if ($this->_dbg) {
        $this->_errors[] = $pp;
        $this->_errors[] = $p;
      };
      return false;
    };
    if (!file_exists($pp)) {
      $this->_errors[] = "Invalid path.";
      if ($this->_dbg) {
        $this->_errors[] = $pp;
        $this->_errors[] = $p;
      };
      return false;     
    };
    $this->fPath = $pp;
    return true;
  }
  
  function addHeader($key, $value = '') {
    $this->_headers[$key] = $value;
  }

  function addBody($value) {
    $this->_body .= $value;
  }

  function getHeaders() {
    return $this->_headers;
  }

  function getBody() {
    return $this->_body;
  }
  
  function getMime() {
    switch ($this->mode) {
      case 'mp4':
        return 'video/mp4';
        break;
    }
    return 'video/unknown';     
  }
  
  function flushHeaders() {
      foreach ($this->getHeaders() as $key=>$val) {
        if ($val) {
          header($key.': '.$val);  
        } else {
          header($key);  
        }
      };
      return $this;
  }

  function flushBody() {
      echo $this->_body;
      return $this;
  }

  function setFPath(string $path) {
    $this->fPath = $path;
    return $this;
  }
  
  function retrieve() {
      //sleep(100);die();
      /*
    if(!$this->checkRequestedPath()){
      $this->addHeader($this->_sp." 404 Not Found");
      $this->addHeader('Content-type', $this->getMime());
      $this->addHeader("x-debug-message","requested path error");
      $this->addBody(implode("\n", $this->_errors));
      $this->flushHeaders()->flushBody();
      return;      
    }*/
    if (!$fp = fopen($this->fPath, "rb")) {
      $this->addHeader($this->_sp." 404 Not Found");
      $this->addHeader('Content-type', $this->getMime());
      $this->addHeader("x-debug-message","file cant be opened");
      $this->flushHeaders()->flushBody();
      return;
    };
    $size = filesize($this->fPath);
    $length = $size;
    $start = 0;  
    $end = $size - 1; 
    $this->addHeader('Content-type', $this->getMime());
    $this->addHeader('Accept-Ranges', "0-$length");
    if (isset($_SERVER['HTTP_RANGE'])) {
      //sleep(100);die();
      $c_start = $start;
      $c_end = $end;
      list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
      if (strpos($range, ',') !== false) {
        $this->addHeader($this->_sp . " 416 Requested Range Not Satisfiable");
        $this->addHeader("Content-Range"," bytes $start-$end/$size");
        $this->addHeader("x-debug-message","malformed range?");
        $this->flushHeaders()->flushBody();
        return;
      };
      if ($range == '-') {
        $c_start = $size - substr($range, 1);
      } else {
        $range = explode('-', $range);
        $c_start = $range[0];
        $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
      };
      $c_end = ($c_end > $end) ? $end : $c_end;
      if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
        $this->addHeader($this->_sp . " 416 Requested Range Not Satisfiable");
        $this->addHeader("Content-Range", "bytes $start-$end/$size");
        $this->addHeader("x-debug-message","requested range error");
        $this->flushHeaders()->flushBody();
        return;
      };
      $start = $c_start;
      $end = $c_end;
      $length = $end - $start + 1;
      fseek($fp, $start);
      $position = ftell($fp);
      $this->addHeader($this->_sp . " 206 Partial Content");
      //http_response_code(206);
      $buffer = 1024 * 8;
      $chunk = $buffer * 1024;
      if ($position + $chunk > $end) {
        $chunk = $end - $position + 1;  
      }; 
      $chunkend = $start+$chunk;
      $this->addHeader("Content-Range"," bytes $start-$chunkend/$size");
      $this->addHeader("Content-Length", $chunk);
      $this->flushHeaders();
      while (!feof($fp) && ($p = ftell($fp)) <= $end) {
        if ($p + $buffer > $end) {
          $buffer = $end - $p + 1;
        }
        //set_time_limit(0);

        //$this->addBody(fread($fp, $buffer));
        echo fread($fp, $buffer);
        flush();
      };
      fclose($fp);
      return;
    };
    
    $this->addHeader("Content-Range","bytes $start-$end/$size");
    $this->addHeader("Content-Length","$length");
    $buffer = 1024 * 8;
    $this->flushHeaders();
    while(!feof($fp) && ($p = ftell($fp)) <= $end) {
      if ($p + $buffer > $end) {
        $buffer = $end - $p + 1;
      }
      set_time_limit(0);
      echo fread($fp, $buffer);
      flush();
      break;
    };
    fclose($fp);
    //die('??');
  }
/*
  function get($path, $mime = 'video/mp4') {
    //set_time_limit(6);
    $access_granted = true;
    if ($access_granted) {
      if ($fp = fopen($path, "rb")) {
        $size = filesize($path); 
        $length = $size;
        $start = 0;  
        $end = $size - 1; 
        header('Content-type: ' . $mime);
        header("Accept-Ranges: 0-$length");
        if (isset($_SERVER['HTTP_RANGE'])) {
            
          $c_start = $start;
          $c_end = $end;
          list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
          if (strpos($range, ',') !== false) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            exit;
          }
          if ($range == '-') {
            $c_start = $size - substr($range, 1);
          } else {
            $range = explode('-', $range);
            $c_start = $range[0];
            $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
          }
          $c_end = ($c_end > $end) ? $end : $c_end;
          if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            exit;
          }
          $start = $c_start;
          $end = $c_end;
          $length = $end - $start + 1;
          fseek($fp, $start);
          header('HTTP/1.1 206 Partial Content');
        }
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: ".$length);
        $buffer = 1024 * 8;
        while(!feof($fp) && ($p = ftell($fp)) <= $end) {
          if ($p + $buffer > $end) {
            $buffer = $end - $p + 1;
          }
          set_time_limit(0);
          echo fread($fp, $buffer);
          flush();
        }
        fclose($fp);
        exit();
      } else {
        die('file not found');
      }
    } else {
      die('forbidden');
    }

  }
*/
}
