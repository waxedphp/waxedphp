<?php
namespace Waxedphp\Waxedphp\Php;

/*
 *
 * Beware, this is not very optimized yet!
 *
 */

/**
 * This is the implementation of the server side part of
 * Resumable.js client script, which sends/uploads files
 * to a server in several chunks.
 *
 * The script receives the files in a standard way as if
 * the files were uploaded using standard HTML form (multipart).
 *
 * This PHP script stores all the chunks of a file in a temporary
 * directory (`temp`) with the extension `_part<#ChunkN>`. Once all
 * the parts have been uploaded, a final destination file is
 * being created from all the stored parts (appending one by one).
 *
 * @author Gregory Chris (http://online-php.com)
 * @email www.online.php@gmail.com
 *
 * @editor Bivek Joshi (http://www.bivekjoshi.com.np)
 * @email meetbivek@gmail.com
 */

class Resumable {
////////////////////////////////////////////////////////////////////
// THE FUNCTIONS
////////////////////////////////////////////////////////////////////

  /**
   *
   * Logging operation - to a file (upload_log.txt) and to the stdout
   * @param string $str - the logging string
   */
  function _log($str) {

      // log to the output
      $log_str = date('d.m.Y').": {$str}\r\n";
      echo $log_str;

      // log to file
      if (($fp = fopen(APPPATH . 'temp/upload_log.txt', 'a+')) !== false) {
          fputs($fp, $log_str);
          fclose($fp);
      }
  }

  /**
   *
   * Delete a directory RECURSIVELY
   * @param string $dir - directory path
   * @link http://php.net/manual/en/function.rmdir.php
   */
  function rrmdir($dir) {
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

  /**
   *
   * Check if all the parts exist, and
   * gather all the parts of the file together
   * @param string $temp_dir - the temporary directory holding all the parts of the file
   * @param string $fileName - the original file name
   * @param string $chunkSize - each chunk size (in bytes)
   * @param string $totalSize - original file size (in bytes)
   */
  function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize,$total_files) {

      // count all the parts of this file
      $total_files_on_server_size = 0;
      $temp_total = 0;
      foreach(scandir($temp_dir) as $file) {
          $temp_total = $total_files_on_server_size;
          $tempfilesize = filesize($temp_dir.'/'.$file);
          $total_files_on_server_size = $temp_total + $tempfilesize;
      }
      // check that all the parts are present
      // If the Size of all the chunks on the server is equal to the size of the file uploaded.
      if ($total_files_on_server_size >= $totalSize) {
      // create the final destination file
          if (($fp = fopen($temp_dir.'/../'.$fileName, 'w')) !== false) {
              for ($i=1; $i<=$total_files; $i++) {
                  fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
                  $this->_log('writing chunk '.$i);
              }
              fclose($fp);
              file_put_contents($temp_dir.'/../'.$fileName . '.txt', print_r($_POST, true));
              $this->_log('destination file: '.$temp_dir.'/'.$fileName);
          } else {
              $this->_log('cannot create the destination file');
              return false;
          }

          // rename the temporary directory (to avoid access from other
          // concurrent chunks uploads) and than delete it
          if (rename($temp_dir, $temp_dir.'_UNUSED')) {
              $this->rrmdir($temp_dir.'_UNUSED');
          } else {
              $this->rrmdir($temp_dir);
          }
      }

  }

  private function ascii($text) {
    return preg_replace("/[^a-zA-Z0-9\-]+/", "", $text);
  }

  private function intval($text) {
    return intval($text);
  }

////////////////////////////////////////////////////////////////////
// THE SCRIPT
////////////////////////////////////////////////////////////////////

  function dispatch() {

    //check if request is GET and the requested chunk exists or not. this makes testChunks work
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if(!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier'])!='')){
          return false;
            $_GET['resumableIdentifier']='';
        }
        $temp_dir = APPPATH . 'temp/' . $this->ascii($_GET['resumableIdentifier']);
        if(!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename'])!='')){
            $_GET['resumableFilename']='';
        }
        if(!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber'])!='')){
            $_GET['resumableChunkNumber']='';
        }
        $chunk_file = $temp_dir.'/' . $this->ascii($_GET['resumableFilename']) . '.part' . $this->intval($_GET['resumableChunkNumber']);
        if (file_exists($chunk_file)) {
             header("HTTP/1.0 200 Ok");
           } else {
             header("HTTP/1.0 404 Not Found");
           }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!(isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier'])!='')){
            return false;
        }
    } else {
      return false;
    }

    // loop through files and move the chunks to a temporarily created directory
    if (!empty($_FILES)) foreach ($_FILES as $file) {

        // check the error status
        if ($file['error'] != 0) {
            $this->_log('error '.$file['error'].' in file ' . $this->ascii($_POST['resumableFilename']));
            continue;
        }

        // init the destination file (format <filename.ext>.part<#chunk>
        // the file is stored in a temporary directory
        if(isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier'])!=''){
            $temp_dir = APPPATH . 'temp/' . $this->ascii($_POST['resumableIdentifier']);
        }
        $dest_file = $temp_dir.'/'.$this->ascii($_POST['resumableFilename']).'.part'.$this->ascii($_POST['resumableChunkNumber']);

        // create the temporary directory
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        // move the temporary file
        if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
            $this->_log('Error saving (move_uploaded_file) chunk '.$this->intval($_POST['resumableChunkNumber']).' for file '.$this->ascii($_POST['resumableFilename']));
        } else {
            // check if all the parts present, and create the final destination file
            $this->createFileFromChunks($temp_dir, $this->ascii($_POST['resumableFilename']),$this->intval($_POST['resumableChunkSize']), $this->intval($_POST['resumableTotalSize']),$this->intval($_POST['resumableTotalChunks']));
        }
    }
    return true;
  }

}





/*
 *dzuuid,dzchunkindex,dztotalfilesize,dzchunksize,dztotalchunkcount,dzchunkbyteoffset
 * file, filename
 *
 * -----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="dzuuid"

e12b15e3-11cc-4681-9bde-b1408b0f863a
-----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="dzchunkindex"

41
-----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="dztotalfilesize"

349090695
-----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="dzchunksize"

100
-----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="dztotalchunkcount"

3490907
-----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="dzchunkbyteoffset"

4100
-----------------------------38788328204007675859678093516
Content-Disposition: form-data; name="file"; filename=".xxx-whatever.mp4"
Content-Type: application/octet-stream
*
*/
