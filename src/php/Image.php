<?php
namespace JasterStary\Waxed\php;


class Image {


  function get($path, $mime = 'image/jpg') {
    //set_time_limit(6);
    $access_granted = true;
    if ($access_granted) {
      if ($fp = fopen($path, "rb")) {
        $size = filesize($path); 
        $length = $size;
        $start = 0;  
        $end = $size - 1; 
        header('Content-type: ' . $mime);
        echo file_get_contents($path);
        flush();
        fclose($fp);
        exit();
      } else {
        die('file not found');
      }
    } else {
      die('forbidden');
    }

  }

}
