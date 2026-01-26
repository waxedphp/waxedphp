<?php
namespace Waxedphp\Waxedphp\Php;

function str_last_replace($search, $replace, $subject) {
  $pos = strrpos($subject, $search);

  if($pos !== false) {
    $subject = substr_replace($subject, $replace, $pos, strlen($search));
  };

  return $subject;
}


