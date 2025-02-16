# jsonviewer

Formats & syntax highlights JSON.

Port of Ben Hollis's JSONView extension for Firefox.

MIT license


### HTML:

```

<div class="jsonviewer"
  data-name="payload"
  data-collapsed="true"
>
</div>

```

### PHP:

```

$this->waxed->display([
  'payload' => [
    'time' => time() * 1000,
    'date' => date('Y-m-d H:i:s'),
    'hash' => md5('ok'),
    'message' => 'whatever',
  ],
], 'template');

```


http://yesmeck.github.io/jquery-jsonview/
