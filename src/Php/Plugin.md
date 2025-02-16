# Class Waxed/Plugin



## Method "uses"
Defines list of Waxed JS plugins, which should be deployed on page.
Such plugins provide rich additional functionality to the html template.

### variables:
$plugins (which should be deployed on page)

```
$this->waxed->plugin->uses('base', 'jsonviewer', 'markdown');

```

---


## Method dispatch
Returns HTTP response with components of Waxed JS plugins, according to provided URL.
In case of JS and CSS components, multiple files could be concatenated together.
Method can also provide internal content of plugins, such as images or fonts,
or even another JS, CSS called on demand from parent script.

### variables:
$url:

```
  // controller method for plugin route:
  public function inc() {
    $a = implode('/', func_get_args());
    $this->waxed->plugin->dispatch($a);
  }

```



---



## Method "mode"
Designates, how the output will be delivered.
This method should not be called directly.

Available modes are:

- html
- html-include
- js
- css
- eot, ttf, woff, woff2, otf, svg, gif, png, jpg, jpeg
- dbg

```
$this->waxed->plugin->mode('html');

```



## Method getList

### variables:

---
