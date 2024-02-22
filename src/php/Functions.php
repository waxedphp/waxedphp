<?php
namespace Waxed\Waxed\php;

function str_last_replace($search, $replace, $subject) {
  $pos = strrpos($subject, $search);

  if($pos !== false) {
    $subject = substr_replace($subject, $replace, $pos, strlen($search));
  };

  return $subject;
}
