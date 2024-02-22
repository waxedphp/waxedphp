<?php
namespace JasterStary\Waxed\php\Uploaders;

/**
 * Class maintaining chunked uploads from Dropzone plugin.
 *
 */
class Dropzone {

  /**
   * @var ?string $writablePath
   */
  protected ?string $writablePath = null;

  /**
   * @var ?string $lassName
   */
  protected ?string $className = null;

  /**
   * @var array<callable> $callbacks
   */
  protected array $callbacks;

  /**
   * @var int $baseChunkIndex
   */
  protected int $baseChunkIndex = 0;

  /**
  * constructor
  *
  */
  function __construct() {
    $reflect = new \ReflectionClass($this);
    $this->className = $reflect->getShortName();
  }

  /**
  * set writable path
  *
  * @param string $path
  * @return object
  */
  function setWritablePath(string $path): object {
    $path = realpath($path);
    $this->writablePath = ($path)?$path:null;
    return $this;
  }

  /**
  * set base chunk index
  *
  * @param int $index
  * @return object
  */
  function setBaseChunkIndex(int $index): object {
    $this->baseChunkIndex = $index;
    return $this;
  }


  /**
  * set callback
  *
  * @param callable $onDone
  * @return object
  */
  function setCallback(string $name, callable $onDone): object {
    $this->callbacks[$name] = $onDone;
    return $this;
  }

  /**
  *
  * Logging operation - to a file (upload_log.txt) and to the stdout
  *
  * @param string $str - the logging string
  * @return void
  */
  function log(string $str) {
    $class = $this->className;
    $log_str = date('YmdHis').": {$class} {$str}\r\n";
    if (($fp = fopen($this->writablePath . '/upload_log.txt', 'a+')) !== false) {
      fputs($fp, $log_str);
      fclose($fp);
    }
  }

  /**
  *
  * Delete a directory RECURSIVELY
  *
  * @param string $dir - directory path
  * @return void

  function rrmdir(string $dir) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . "/" . $object) == "dir") {
              $this->rrmdir($dir . "/" . $object);
          } else {
              unlink($dir . "/" . $object);
          }
        }
      }
      reset($objects);
      rmdir($dir);
    }
  }
  */

  /**
  *
  * Check if all the parts exist, and
  * gather all the parts of the file together
  *
  * @param string $temp_dir - the temporary directory holding all the parts of the file
  * @param array<mixed> $fileData
  * @param int $chunkSize - each chunk size (in bytes)
  * @param int $totalSize - original file size (in bytes)
  * @param int $total_files
  * @return void;
  */
  function createFileFromChunks(
    string $temp_dir, array $fileData, int $chunkSize, int $totalSize, int $total_files
  ): void {
    // count all the parts of this file
    $files = scandir($temp_dir);
    if (!$files) {
      return;
    }
    if (count($files)<($total_files+2)) return;
    $files = array_flip($files);
    $total_filesize = 0;$count_files = 0;
    foreach($files as $file => $val) {
      $fullpath = $temp_dir.'/'.$file;
      if (is_dir($fullpath)) unset($files[$file]);
      $a = explode('.', $file);
      if ((is_file($fullpath))&&($a[1]==='part')) {
        $total_filesize += $files[$file] = filesize($fullpath);
        $count_files++;
      } else unset($files[$file]);
    }
    // check that all the parts are present
    // If the Size of all the chunks on the server is equal to the size of the file uploaded.
    if (($count_files >= $total_files) && ($total_filesize >= $totalSize)) {
      // create the final destination file
      if (($fp = fopen($temp_dir.'/final.bin', 'w')) !== false) {
        foreach($files as $file => $size) {
          $fullpath = $temp_dir.'/'.$file;
          $content = file_get_contents($fullpath);
          if (!$content) {

          } else {
            fwrite($fp, $content);
          }
          //$this->log('writing chunk ' . $file);
        };
        fclose($fp);
        foreach($files as $file => $size) {
          $fullpath = $temp_dir.'/'.$file;
          if (is_file($fullpath)) unlink($fullpath);
        };
        $this->storeInfo($temp_dir, $fileData);
        $this->done($temp_dir, $fileData);
        $this->log('destination file: '.$temp_dir.'/final.bin');
      } else {
        $this->log('Cannot create the destination file: ' . $temp_dir . '/final.bin');
        return;
      }
    }
  }

  /**
  * ascii
  *
  * @param string $text
  * @return string
  */
  private function ascii(string $text): string {
    $a = preg_replace("/[^a-zA-Z0-9\-]+/", "", $text);
    return ($a)?$a:'';
  }

  /**
  * intval
  *
  * @param string|int $text
  * @return int
  */
  private function intval($text): int {
    return intval($text);
  }

  /**
  * padded
  *
  * @param string|int $input
  * @return string
  */
  private function padded(string|int $input): string {
    return str_pad((String)intval($input), 10, "0", STR_PAD_LEFT);
  }

  /**
  * dispatch
  *
  * @return void
  */
  function dispatch(): void {
    if (!$this->writablePath) {
      $this->log('Writable path misconfigured.');
      die();
    };
    $this->log('FILES ' . print_r($_FILES, true));
    //check if request is GET and the requested chunk exists or not. this makes testChunks work
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if((isset($_GET['dzuuid']) && trim($_GET['dzuuid'])!='')){//resumableIdentifier
        if ($this->checkDropzoneChunks($_GET)){
          $this->trigger('onCheckFound');
        } else {
          $this->trigger('onCheckNotFound');
        };
        return;
      }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if((isset($_POST['dzuuid']) && trim($_POST['dzuuid'])!='')){//resumableIdentifier
        $this->storeDropzoneChunks();
      } else {
        $this->storeFiles();
      }
    } else {
      return;
    }
  }

  /**
  * store files
  *
  * @return void
  */
  private function storeFiles(): void {
    if (!empty($_FILES)) foreach ($_FILES as $file) {
      $this->trigger('onFile', $file, $this->extractBytes($file['tmp_name']));
      if ($file['error'] != 0) {
          $this->log('error '.$file['error'].' in file ' . $this->ascii($_POST['dzuuid']));
          $this->trigger('onError', $file);
          continue;
      }
      $temp_dir = $this->writablePath . '/' . $this->get_guid();//$this->ascii($file['name']);
      // create the temporary directory
      if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0777, true);
      }
      $dest_file = $temp_dir . '/';
      $dest_file .= 'final.bin';
      // move the temporary file
      if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
        $this->log(
          'Error saving (move_uploaded_file) file '
          . $this->ascii($file['name'])
        );
        $this->trigger('onError', $file, $dest_file);
      } else {
        $this->storeInfo($temp_dir, $file);
        $this->done($temp_dir, $file);
      }
    }
    return;
  }

  /**
  * store chunks
  *
  * @return void
  */
  function storeDropzoneChunks(): void {
    // loop through files and move the chunks to a temporarily created directory
    if (!empty($_FILES)) foreach ($_FILES as $file) {
      // check the error status
      if ($file['error'] != 0) {
          $this->log('error '.$file['error'].' in file ' . $this->ascii($_POST['dzuuid']));
          $this->trigger('onError', $file);
          //throw new \Exception('What the hell.');
          continue;
      }

      // init the destination file (format <filename.ext>.part<#chunk>
      // the file is stored in a temporary directory
      if(
        (isset($_POST['dzuuid'])) && (trim($_POST['dzuuid'])!='')
        &&(isset($_POST['dzchunkindex'])) && (trim($_POST['dzchunkindex'])!='')
        //&&(isset($_POST['dzchunkbyteoffset'])) && (trim($_POST['dzchunkbyteoffset'])!='')
      ){
        if (!isset($_POST['dzchunkbyteoffset'])) $this->baseChunkIndex = 1;
        $chunkindex = intval($_POST['dzchunkindex']);
        if ($chunkindex === $this->baseChunkIndex) {
          $this->trigger('onFirstDropzoneChunk', $file, $this->extractBytes($file['tmp_name']));
        } else {
          $this->trigger('onDropzoneChunk', $file, $chunkindex);
        }
        $temp_dir = $this->writablePath . '/' . $this->ascii($_POST['dzuuid']);
        $dest_file = $temp_dir . '/';
        $dest_file .= '.part';
        $dest_file .= '.'.$this->padded($_POST['dzchunkindex']);
        //$dest_file .= '.'.$this->intval($_POST['dzchunkbyteoffset']);
        $dest_file .= '.bin';
        // create the temporary directory
        if (!is_dir($temp_dir)) {
          mkdir($temp_dir, 0777, true);
        }
      } else {
        $this->trigger('onError', $file, $_POST);
        return;
      }

      // move the temporary file
      if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
        $this->log(
          'Error saving (move_uploaded_file) chunk '
          . $this->padded($_POST['dzchunkindex'])
          . ' for file '
          . $this->ascii($_POST['dzuuid'])
        );
        $this->trigger('onError', $file, $dest_file);
      } else {
        // check if all the parts present, and create the final destination file
        //print_r($file);die();
        $this->createFileFromChunks(
          $temp_dir,
          $file,
          $this->intval($_POST['dzchunksize']),//resumableChunkSize
          $this->intval($_POST['dztotalfilesize']),//resumableTotalSize
          $this->intval($_POST['dztotalchunkcount'])//resumableTotalChunks
        );
      }
    }
    return;
  }

  /**
  * check chunks
  *
  * @return bool
  */
  function checkDropzoneChunks(array $data): bool {
    if (!isset($data['dzuuid'])) return false;
    if (!isset($data['dzchunkindex'])) return false;
    if (!isset($data['dzchunksize'])) return false;
    $the_dir = $this->writablePath . '/' . $this->ascii($data['dzuuid']);
    if (!is_dir($the_dir)) return false;
    $the_file = $the_dir . '/';
    $the_file .= '.part';
    $the_file .= '.'.$this->padded($data['dzchunkindex']);
    //$dest_file .= '.'.$this->intval($_POST['dzchunkbyteoffset']);
    $the_file .= '.bin';
    if (!is_file($the_file)) return false;
    if (intval($data['dzchunksize']) != filesize($the_file)) {
      return false;
    };
    return true;
  }

  /**
  * store info
  *
  * @param string $temp_dir
  * @param array<mixed> $fileData
  * @return object
  */
  function storeInfo(string $temp_dir, array &$fileData): object {
    $fileData = [
      'file' => $fileData,
      'headers' => getallheaders(),
      'post' => $_POST,
      'ip' => $_SERVER['REMOTE_ADDR'],
      'time' => date('Y-m-d H:i:s')
    ];
    file_put_contents($temp_dir.'/info.json', json_encode($fileData, JSON_PRETTY_PRINT));
    return $this;
  }

  /**
  * done
  *
  * @param string $path
  * @param array<mixed> $fileData
  * @return void;
  */
  function done(string $path, array $fileData): void {
    $newPath = $path . '-DONE-' . date('YmdHis');
    rename($path, $newPath);
    $this->trigger('onDone', $newPath, $fileData);
  }

  /**
  * trigger
  *
  * @param string $name
  * @param array<mixed> $arguments
  * @return object
  */
  private function trigger(string $name, ...$arguments): object {
    if (isset($this->callbacks[$name])){
      call_user_func_array($this->callbacks[$name], $arguments);
    }
    return $this;
  }

  /**
  * get guid
  *
  * @return string
  */
  private function get_guid(): string {
    if (function_exists('com_create_guid') === true) {
      $guid = com_create_guid();
      if ($guid) {
        return trim($guid, '{}');
      }
    }
    $data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  /**
  * extract bytes
  *
  * @param string $filename
  * @param int $count
  * @return ?string
  */
  private function extractBytes(string $filename, int $count = 10): ?string {
    $buf = ''; $buflen = 10000;
    $f = fopen($filename, "r");
    if (!$f) return null;
    $sequence = fread($f, $buflen);
    fclose($f);
    if (!$sequence) return null;
    $sequence = bin2hex(substr($sequence, 0, min($count, strlen($sequence))));
    $this->log('SEQUENCE:'.$sequence);
    return $sequence;
  }
  //00000020667479706973 mp4
  //00000020667479706973
  //ffd8ffe000104a464946 jpg
  //ffd8ffe000104a464946
  //ffd8ffe000104a464946
  //3c73766720786d6c6e73 svg
  //89504e470d0a1a0a0000 png
  //89 50 4E 47 0D 0A 1A 0A PNG
  //89504e470d0a1a0a0000



}
