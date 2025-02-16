<?php
ob_end_clean();
$turn = 0;
if (isset($_GET['_turn_'])) {
  $turn = intval($_GET['_turn_']);
}
for ($i = 1; $i <= 100; $i++) {
  $Line = $i . '/' . $turn . ' ' . date('Y-m-d H:i:s');
  echo '<chunk>' . $Line . '</chunk>';
  //echo '<message>' . json_encode(['line' => $Line,'turn' => $turn]) . '</message>';
  //echo '<script>document.title = "' . $Line . '";</script>';
  flush();
  //sleep(rand(1, 2));
  
  usleep(rand(1, 10) * 10);
};
