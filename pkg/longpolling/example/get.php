<?php
// must be here, to allow flush before script ends:
ob_end_clean();

// optionaly, grab the "turn" parameter.
$turn = 0;
if (isset($_GET['_turn_'])) {
  $turn = intval($_GET['_turn_']);
}

// looong method, sometimes flushing results:
for ($i = 1; $i <= 10000; $i++) {
  $Line = $i . '/' . $turn . ' ' . date('Y-m-d H:i:s');

  // simply flush the line:
  echo '<chunk>' . $Line . '</chunk>';

  // or flush the JSON object:
  //echo '<chunk>' . json_encode(['line' => $Line,'turn' => $turn]) . '</chunk>';

  // or flush the function, be carefull with that exec, Eugene:
  //echo '<chunk>document.title = "' . $Line . '";</chunk>';

  // flush must be here:
  flush();

  // mimic the looong evaluations:
  //sleep(rand(1, 3));
  usleep(rand(1, 10) * 1000);
};
// and thats all...
