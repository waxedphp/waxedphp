### Display
Template is drawn inside selected space, and filled with data.

```
$array_of_data = [
  'time' => date('H:i:s'),
];
$template = 'hello.html';
$this->waxed->display($array_of_data, $template);

```

This is, how the template "hello.html" can look like:

```
<p>The time is: {{time}}</p>

```
