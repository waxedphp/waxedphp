<?php
namespace Waxedphp\Waxedphp\php\Setters;

class Paging extends AbstractSetter {

  /**
  * paging
  *
  * @param int $onPage
  * @param int $total
  * @param int $current
  * @return
  */
  public static function paging(int $onPage, int $total, int $current) {
    $a = [];
    $c = ceil($total/$onPage);
    $start = max(0,$current - 3);
    $end = min($c,$current + 1 + 3);
    for ($i = $start; $i < $end; $i++) {
      $a[$i] = [
        '_page' => $i,
        'label' => ($i+1).''
      ];
      if ($i == $current) {
        $a[$i]['active'] = 'active';
      };
    }
    $a = array_values($a);
    array_unshift($a, [
      '_page' => 0,
      'label' => '&laquo;',
    ]);
    array_push($a, [
      '_page' => $c - 1,
      'label' => '&raquo;',
    ]);
    if ($current == 0) {
      $a[0]['active'] = 'disabled';
    } else if ($current == $c - 1) {
      $a[count($a)-1]['active'] = 'disabled';
    }
    return array_values($a);
  }

  /**
  * page
  *
  * @param int $page
  * @return
  */
  public function page(int $page = 0) {
    if (isset($_POST['_page'])) {
      $page = intval($_POST['_page']);
    };
    return $page;
  }


  /**
  * value
  *
  * @param mixed $value
  * @return array<mixed>
  */
  public function value(mixed $value): array {
    return $value;
  }

}
