<?php
namespace Waxedphp\Waxedphp\php\Setters;

class TimeChart extends AbstractSetter {


  private array $col = [];
  private array $datasets = [];
  private array $labels = [];
  private array $colors = [];


  function setColors($aColors) {
    $this->colors = $aColors;
    return $this;
  }

  function addDataset($label,$data, $dd='items', $key='', $timestamp='timestamp') {
    $data = self::_traverse($data, $dd);
    if (!is_array($data)) {
      return this;
    };
    $n = count($this->datasets);
    $this->datasets[$n] = [];
    $this->labels[$n] = $label;
    foreach ($data as $row) {
      if ((isset($row[$timestamp]))&&(isset($row[$key]))) {
        if (!isset($this->col[intval($row[$timestamp])])) {
          $this->col[intval($row[$timestamp])] = date('Y-m-d H:i:s', intval($row['timestamp']));
        };
        $this->datasets[$n][intval($row[$timestamp])] = intval($row[$key]);
      };
    };
    return $this;
  }

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

  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value): array {
    //$label, $data, $dd = 'items', $key = '', $timestamp = 'timestamp'
    foreach ($value as $label => $data) {
      $this->addDataset($label, $data);
    }
    return $this->forChartJs();
  }

}
