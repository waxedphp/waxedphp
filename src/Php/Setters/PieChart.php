<?php
namespace Waxedphp\Waxedphp\Php\Setters;

class PieChart extends AbstractSetter {

  private array $col = [];
  private array $datasets = [];
  private array $labels = [];
  private array $colors = [];

  function setColors($aColors) {
    $this->colors = $aColors;
    return $this;
  }

  function addDataset($label,$data, $dd='', $keys=['used','free']) {
    $data = self::_traverse($data, $dd);
    if (!is_array($data)) {
      return false;
    };
    $n = count($this->datasets);
    $this->datasets[$n] = [];
    $this->labels[$n] = $label;
    foreach ($data as $key=>$val) {
      if (in_array($key,$keys)) {
        if (!isset($this->col[$key])) {
          $this->col[$key] = $key;
        };
        $this->datasets[$n][$key] = $val;
      };
    };
    return $this;
  }

  function forChartJs() {
    $re = [];
    ksort($this->col);
    $re['labels'] = array_values($this->col);
    $re['datasets'] = [];
    foreach ($this->datasets as $n=>$dataset) {
      foreach ($this->col as $k=>$v) {
        if (!isset($this->datasets[$n][$k])) {
          $this->datasets[$n][$k] = null;
        };
      };
      ksort($this->datasets[$n]);
      $re['datasets'][] = [
          'label' => $this->labels[$n],
          'data' => array_values($this->datasets[$n]),
          'backgroundColor' => $this->colors,
          'hoverOffset' => 4,
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
    //$label,$data, $dd='', $keys=['used','free']
    foreach ($value as $label => $data) {
      $this->addDataset($label, $data);
    }
    return $this->forChartJs();
  }

}
/*
[
            'labels'=>['Used','Available'],
            'datasets' => [[
              'label'=>$hs['hosting'],
              'data'=> [$hs['used'], $hs['free']],
              'backgroundColor' => [
                'rgb(255, 99, 132)',
                'rgb(255, 205, 86)'
              ],
              'hoverOffset' => 4,
            ],
]
 */
