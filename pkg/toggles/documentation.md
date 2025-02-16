# toggles

Create easy toggle buttons that you can click, drag, animate, use to toggle checkboxes and more.

From Waxed, there is added possibility to collect values bitwise.

https://simontabor.com/labs/toggles/
https://github.com/simontabor/jquery-toggles

MIT License

#### HTML:

```

<div class="waxed-toggles toggle toggle-light"
  style="width:60px;float:left;margin:20px;"
  data-name="payload"
  data-text_on="on"
  data-text_off="off"
  data-input="input[name=payload]"
  data-bitwise="4"
></div>


```

#### PHP:

```

$this->waxed->display([
'payload' => (2|8),
'payload1' => true,
'payload2' => false,
'payload3' => true,
'payload4' => false,
'payload5' => true, 
], 'template');


```

[download](https://github.com/simontabor/jquery-toggles)
