<?php
return [
  'payload' => [
    'time' => time() * 1000,
    'date' => date('Y-m-d H:i:s'),
    'hash' => md5('ok'),
    'message' => 'whatever',
  ],
];
