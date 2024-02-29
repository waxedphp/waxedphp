<?php
namespace Waxedphp\Waxedphp\Setters;

class Tabulator extends AbstractSetter {

  /**
   * @var array<mixed> $col
   */
  private array $col = [];

  /**
   * @var array<mixed> $val
   */
  private array $val = [];

  /**
   * @var array<mixed> $fmt
   */
  private array $fmt = [];

  /**
   * @var array<mixed> $colors
   */
  private array $colors = [];

  /**
   * @var array<mixed> $setup
   */
  private array $setup = [];

  /**
   * @var array<mixed> $_formatters
   */
  private array $_formatters = [

    'plaintext'=>[],
    'textarea'=>[],
    'html'=>[],
    'money'=>[
      "decimal"=>",",
      "thousand"=>".",
      "symbol"=>"£",
      "symbolAfter"=>"p",
      "precision"=>false,
    ],
    'image'=>[
      "height"=>"50px",
      "width"=>"50px",
      "urlPrefix"=>"http://website.com/images/",
      "urlSuffix"=>".png",
    ],
    'link'=>[
      "labelField"=>"name",
      "urlPrefix"=>"mailto://",
      "target"=>"_blank",
    ],
    'datetime'=>[
      "inputFormat"=>"yyyy-MM-dd HH:ss",
      "outputFormat"=>"dd/MM/yy",
      "invalidPlaceholder"=>"(invalid date)",
      "timezone"=>"America/Los_Angeles",
    ],
    'datetimediff'=>[
      "inputFormat"=>"yyyy-MM-dd",
      "units"=>["months", "days", "hours"],
      "humanize"=>true,
      "invalidPlaceholder"=>"(invalid date)",
    ],
    'tickCross'=>[
      "allowEmpty"=>true,
      "allowTruthy"=>true,
      "tickElement"=>"<i class='fa fa-check'></i>",
      "crossElement"=>"<i class='fa fa-times'></i>",
    ],
    'color'=>[],
    'star'=>[
      "stars"=>5
    ],
    'traffic'=>[
      "min"=>0,
      "max"=>10,
      "color"=>["green", "orange", "red"],
    ],
    'progress'=>[
      "min"=>0,
      "max"=>100,
      "color"=>["green", "orange", "red"],
      "legendColor"=>"#000000",
      "legendAlign"=>"center",
    ],
    'lookup'=>[],
    'buttonTick'=>[],
    'buttonCross'=>[],
    'rownum'=>[],
    'handle'=>[],

    'rowSelection'=>[],
    'responsiveCollapse'=>[],
  ];

  /**
  * set locale
  *
  * @param bool $loc
  * @return object
  */
  function setLocale(bool $loc = true): object {
    $this->setup['locale'] = $loc;
    return $this;
  }

  /**
  * set local pagination
  *
  * @param int $paginationSize
  * @return object
  */
  function setLocalPagination(?int $paginationSize = null): object {
    $this->setup['pagination'] = true;
    if (is_integer($paginationSize)) $this->setup['paginationSize'] = $paginationSize;
    return $this;
  }

  /**
  * set remote pagination
  *
  * @param string $ajaxURL
  * @param ?int $paginationSize
  * @return object
  */
  function setRemotePagination(string $ajaxURL, ?int $paginationSize = null): object {
    $this->setup['pagination'] = true;
    $this->setup['ajaxURL'] = $ajaxURL;
    if (is_integer($paginationSize)) $this->setup['paginationSize'] = $paginationSize;
    return $this;
  }

  /**
  * set colors
  *
  * @param array<mixed> $aColors
  * @return object
  */
  function setColors(array $aColors): object {
    $this->colors = $aColors;
    return $this;
  }

  /**
  * set setup
  *
  * @param  $cfg
  * @return object
  */
  function setSetup($cfg): object {
    $this->setup = array_merge_recursive($this->setup, $cfg);
    return $this;
  }

  /**
  * get setup
  *
  * @return array
  */
  function getSetup(): array {
    return $this->setup;
  }

  /**
  * add dataset
  *
  * @param string $label
  * @param array<mixed> $data
  * @param string $dd
  * @return object
  */
  function addDataset(string $label, array $data, string $dd=''): object {
    $this->col = [];
    $this->val = [];
    $data = self::_traverse($data, $dd);
    if (is_array($data)) {
      foreach ($data as $row) {
        $this->val[] = $row;
        $cc = array_keys($row);
        foreach ($cc as $c) {
          if (!isset($col[$c])) {
            $this->col[$c] = ['title' => $c, 'field' => $c ];
            if ($c=='link'){
              $this->col[$c]['formatter']='link';
              $this->col[$c]['formatterParams']=[
                'target'=>'_blank',
              ];
            };
          };
        };
      };
    };

    foreach ($this->col as $key => $val) {
      if (isset($this->fmt[$key])) {
        $this->col[$key]['formatter'] = $this->fmt[$key]['formatter'];
        if (isset($this->fmt[$key]['formatterParams'])) {
          $this->col[$key]['formatterParams'] = $this->fmt[$key]['formatterParams'];
        }
      };
    };

    return $this;
  }

  /**
  * set formatter
  *
  * @param string $key
  * @param string $formatter
  * @param array<mixed> $params
  * @return object
  */
  function setFormatter(string $key, string $formatter, $params = []): object {
    if (!isset($this->_formatters[$formatter])) return $this;
    $fmt = $this->_formatters[$formatter];
    $this->fmt[$key]['formatter']=$formatter;
    $oo = [];
    foreach ($params as $k=>$v) {
      if (isset($fmt[$k])) {
        $oo[$k]=$v;
      };
    };
    if (count($oo)) {
      $this->fmt[$key]['formatterParams']=$oo;
    };
    if (isset($this->col[$key])) {
      $this->col[$key]['formatter'] = $this->fmt[$key]['formatter'];
      if (isset($this->fmt[$key]['formatterParams'])) {
        $this->col[$key]['formatterParams'] = $this->fmt[$key]['formatterParams'];
      }
    };
    return $this;
  }

  function loadCSVFile(string $fname, string $delim = ';', string $separ = '"', string $charset = 'UTF-8'):void {
    if (!is_file($fname)) return;
    $this->col = [];
    $this->val = [];
    $cols = [];
    $cnt = 0;
    $this->colNames = [];
    foreach(range('A', 'Z') as $column) {
      $cols[] = $column;
    };
    foreach(range('A', 'Z') as $column1) {
      foreach(range('A', 'Z') as $column2) {
        $cols[] = $column1.$column2;
      };
    };
    $bom = pack("CCC", 0xef, 0xbb, 0xbf);
    ini_set('auto_detect_line_endings',TRUE);
    $handle = fopen($fname, "r");
    $i = 0;
    while (($line = fgets($handle)) !== false) {
      if ($i==0) {
        if (0 === strncmp($line, $bom, 3)) {
          //echo "BOM detected - file is UTF-8\n";
          $charset = 'UTF-8';
          $line = substr($line, 3);
        }
      };
      if ($charset != 'UTF-8') {
        $line = iconv($charset, 'UTF-8', $line);// CP1250
      }
      $row = str_getcsv($line, $delim, $separ);
      $cnt = max($cnt, count($row));
      //print_r($row);
      if ($i==0) {
        $this->colNames = array_combine(array_slice($cols, 0, count($row)), $row);
      } else {
        $this->val[] = array_combine(array_slice($cols, 0, count($row)), $row);
      }
      $i++;
    }
    fclose($handle);
    $row = array_slice($cols, 0, $cnt);
    foreach ($row as $key) {
      $this->col[$key] = ['title' => $key, 'field' => $key];
      if (isset($this->colNames[$key])) {
        $this->col[$key]['title'] = $this->colNames[$key];
      };
      if (isset($this->fmt[$key])) {
        $this->col[$key]['formatter'] = $this->fmt[$key]['formatter'];
        if (isset($this->fmt[$key]['formatterParams'])) {
          $this->col[$key]['formatterParams'] = $this->fmt[$key]['formatterParams'];
        }
      };
    }
  }

  public function getColumns() {
    return $this->col;
  }

  public function getValues() {
    return $this->val;
  }

  /**
  * for tabulator
  *
  * @return array<mixed>
  */
  public function forTabulator(): array {
    $a = [
      'columns' => array_values($this->col),
      'value' => $this->val,
    ];
    if (count($this->setup)) {
      $a['settings'] = $this->setup;
    };
    return $a;
  }

  public function value(mixed $data): array {
    $this->addDataset('dataset', $data);
    return $this->forTabulator();
  }

  /*
  function forChartJs() {
    $re = [];
    ksort($this->col,SORT_NUMERIC);
    $re['labels'] = array_values($this->col);
    $re['datasets'] = [];
    foreach ($this->datasets as $n=>$dataset) {
      foreach ($this->col as $k=>$v) {
        if (!isset($this->datasets[$n][$k])) {
          $this->datasets[$n][$k] = null;
        };
      };
      ksort($this->datasets[$n],SORT_NUMERIC);
      $re['datasets'][] = [
          'label' => $this->labels[$n],
          'data' => array_values($this->datasets[$n]),
          'fill' => false,
          'borderColor' => $this->colors[min($n,count($this->colors)-1)],
          'tension' => 0.1,
      ];
    };
    return $re;
  }
  */

}

