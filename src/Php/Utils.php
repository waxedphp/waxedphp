<?php
namespace Waxedphp\Waxedphp\Php;

class Utils {

  private $base;

  public function __construct($base){
    $this->base = &$base;
  }

  public function sanitize_filename($name) {
    $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $name);
    $name = mb_ereg_replace("([\.]{2,})", '', $name);
    $name = trim($name, '.');
    return $name;
  }

  public function datatablize(&$data, $dd = 'dd') {
    if (isset($data[$dd])) {
      $dt = [];$co = [];
      foreach ($data[$dd] as $r => $row) {
        $o = [];
        if (is_array($row)) {
          $keys = array_keys($row);
          $row = array_values($row);
          foreach ($row as $c => $col) {
            if ($c > 25) break;
            $o[chr(65+$c)] = strip_tags($col);
            if (!isset($co[chr(65+$c)])) {

              $co[chr(65+$c)] = [
                'title' => 'column ' . chr(65+$c),
                'data' => chr(65+$c),
              ];
              if (!is_numeric($keys[$c])) $co[chr(65+$c)]['title'] = $keys[$c];

            };
          };
        };
        if (is_string($row)) {
          $o[chr(65+0)] = strip_tags($row);
          if (!isset($co[chr(65+0)])) {
            $co[chr(65+0)] = [
              'title' => 'column ' . chr(65+0),
              'data' => chr(65+0),
            ];
          };
        };
        $dt[$r] = $o;
      };

      if (empty($co)) {
        $co = [[
          'title' => 'column A',
          'data' => 'A',
        ]];
      };

      $data[$dd] = [
        'value' => $dt,
        'columns' => array_values($co),
      ];
    };
  }

  public static function _traverse($data, $dd) {
    if(!$dd)return $data;
    $dd = explode('/', $dd);
    if (is_array($data)) {
      foreach ($dd as $d) {
        if (isset($data[$d])) {
          $data = $data[$d];
        } else {
          return false;
        };
      };
    };
    return $data;
  }

  public static function SlimSelect() {
    return new SlimSelect();
  }

  public static function Tabulator() {

    $args = func_get_args();
    if (count($args)<1) {
      return new Tabulator();
    };
    $data = $args[0];$dd = '';
    if (isset($args[1])) {
      $dd = $args[1];
    };

    $col = [];
    $val = [];
    $data = self::_traverse($data, $dd);
    if (is_array($data)) {
      foreach ($data as $row) {
        $val[] = $row;
        $cc = array_keys($row);
        foreach ($cc as $c) {
          if (!isset($col[$c])) {
            $col[$c] = ['title' => $c, 'field' => $c ];
            if ($c=='link'){
              $col[$c]['formatter']='link';
              $col[$c]['formatterParams']=[
                'target'=>'_blank',
              ];
            };
          };
        };
      };
    };
    return [
      'columns' => array_values($col),
      'value' => $val,
    ];
  }

  public static function TimestampGraph() {
    return new TimestampGraph();
  }

  public static function PieGraph() {
    return new PieGraph();
  }

  public static function oldTimestampGraph($data, $dd = 'items', $key = 'value', $label = 'graph') {
    $data = self::_traverse($data, $dd);
    if (!is_array($data)) {
      return [
        'labels' => [],
        'datasets' => [],
      ];
    };
    $col = [];
    $val = [];
    foreach ($data as $row) {
      if (isset($row['timestamp'])) {
        $col[intval($row['timestamp'])] = date('Y-m-d H:i:s', intval($row['timestamp']));
        if (isset($row[$key])) {
          $val[intval($row['timestamp'])] = intval($row[$key]);
        };
      };
    };
    ksort($col,SORT_NUMERIC);
    ksort($val,SORT_NUMERIC);
    return [
      'labels' => array_values($col),
      'datasets' => [
        [
          'label' => $label,
          'data' => array_values($val),
          'fill' => false,
          'borderColor' => 'rgb(75, 192, 192)',
          'tension' => 0.1,
        ],
      ],
    ];
  }
          /*
         [
            'labels' => ['A','B','C','D','E','F','G',],
            'datasets' => [
            [
              'label' => 'Visits',
              'data' => [65, 59, 80, 81, 56, 55, 40],
              'fill' => false,
              'borderColor' => 'rgb(75, 192, 192)',
              'tension' => 0.1,
            ],[
              'label' => 'Purchases',
              'data' => [6, 9, 8, 1, 5, 6, 2],
              'fill' => false,
              'borderColor' => 'rgb(255, 205, 86)',
              'tension' => 0.1,
            ],],
          ]
          */

  public function showtime($now) {
      //system('clear');
      //echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
      //system('cls');
      $hours = floor($now / 3600);
      $minutes = floor(($now / 60) % 60);
      $seconds = $now % 60;
      $t = $now . 'sec ';
      if ($hours > 0) {
        $t = sprintf("%02d:%02d:%02d sec", $hours, $minutes, $seconds);
      } else if ($minutes > 0) {
        $t = sprintf("%02d:%02d sec", $minutes, $seconds);
      } else {
        $t = sprintf("%02d sec", $seconds);
      };
      return $t;
  }
  
  function reArrayUploadedFiles($file_post){
    if (!isset($file_post['name'])) return $file_post;
    $isMulti    = is_array($file_post['name']);
    $file_count    = $isMulti?count($file_post['name']):1;
    $file_keys    = array_keys($file_post);

    $file_ary    = [];    //Итоговый массив
    for($i=0; $i<$file_count; $i++)
        foreach($file_keys as $key)
            if($isMulti)
                $file_ary[$i][$key] = $file_post[$key][$i];
            else
                $file_ary[$i][$key]    = $file_post[$key];

    return $file_ary;
  }
  
  function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
  }

}
