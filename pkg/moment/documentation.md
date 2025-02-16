# Moment

Parse, validate, manipulate, and display dates and times in JavaScript.
[https://momentjs.com/]

MIT license


### HTML:

```

<div
  class="waxed-moment"
  data-timezone="America/Toronto"
  data-format="DD.MM.YYYY HH:mm:ss"
  data-value="1565953178000"
  data-name="payload"
>1565953178000</div>

```

### PHP:

```

$this->waxed->display([
  'payload' =>
  [
    'value' => (time() + 0) * 1000,
    'timezone' => 'Europe/Bratislava',
    'format' => 'YYYY/MM/DD (HH:mm:ss) ZZ',
  ],
], 'template');

```


