# Templater

This plugin loads again template.
It is usefull during "waxed->update".
Say, we have a displayed template, where we dont want to redraw everything.
We want only update elements, which data are in waxed->update bulk.
This is easily achieved, in plugins, with setRecord api method.
But there are small parts, where we want to redraw mustache template.

MIT license


### HTML:

```

<div class="waxed-templater"
  data-name="payload"
  data-template="dialog"
></div>

```

### PHP:

```

$this->waxed->display([
  'payload' =>
    [
      'title' => 'Hello World!',
      'message' => 'Lorem ipsum dolor sic amet...',
    ],
], 'template');

```


