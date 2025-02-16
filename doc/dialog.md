### Dialog
Template is drawn as dialog window, and filled with data.

```
$array_of_data = [
  'time' => date('H:i:s'),
];
$template = 'hello.html';
$this->waxed->dialog($array_of_data, $template);

```
