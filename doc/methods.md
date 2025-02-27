
## Basic Available Commands:

- pick
- display
- dialog
- dialogClose
- inspire
- invalidate
- show
- hide
- submit
- reload
- title
- favicon
- fullscreen
- scrollTo
- hashState
- pushState
- flush
- view


////////////////////////
### Pick
Selects element by ID, which we want to work with.

In the following example, it is ID "userbox",
where we want to display the name of logged user,
using template "user.html":

```
$this->waxed->pick('userbox')->display([
      'user' => $this->visitor->getUser(),
      'route' => '/tests/login/ajax',
      'VOCAB' => 'user-1',
    ], 'user');

```

Page contains empty element with ID "userbox":

```
...
<div class="pure-g" >
    <div class="pure-u-1-5" ><p>&nbsp;</p></div>
    <div class="pure-u-2-5" ><p>&nbsp;</p></div>
    <div class="pure-u-2-5" id="userbox" ></div>
</div>
...

```

Template "user.html" looks like this:
```
{{#user.notlogged}}
<p style="margin-right:5px;" >
  <a class="action" href="{{:route}}" data-action="go/login" >
  {{:login}}
  </a>
</p>
{{/user.notlogged}}
{{#user.logged}}
<p style="margin-right:5px;" >
  {{:user.login}}
  <a class="action" href="{{:route}}" data-action="logout" >
  {{:logout}}
  </a>
</p>
{{/user.logged}}

```


////////////////////////
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

////////////////////////
### Dialog
Template is drawn as dialog window, and filled with data.

```
$array_of_data = [
  'time' => date('H:i:s'),
];
$template = 'hello.html';
$this->waxed->dialog($array_of_data, $template);

```

////////////////////////
### Inspire
Existing, drawn yet template is filled with new data.

```
$array_of_data = [
  'time' => date('H:i:s'),
];
$this->waxed->inspire($array_of_data);

```

////////////////////////
### Redraw

### Invalidate
Form is invalidated - labels are filled with description of errors.

```
$array_of_data = [
  'time' => date('H:i:s'),
];
$this->waxed->invalidate($array_of_data);

```

////////////////////////
### Show
Element, selected by ID, is shown.

```
$this->waxed->show($id_of_element);

```

////////////////////////
### Hide
Element, selected by ID, is hidden.

```
$this->waxed->hide($id_of_element);

```

////////////////////////
### Submit
Form, selected by ID, is submitted.


////////////////////////
### Title
Document title is changed.

```
$this->waxed->title($new_title);

```

////////////////////////
### Favicon
Document favicon is changed.

```
$this->waxed->favicon($new_favicon_url);

```

////////////////////////
### Fullscreen
Document try to open to fullscreen.

```
$this->waxed->fullscreen();

```

////////////////////////
### ScrollTo
Document is scrolled to position.

```
$this->waxed->scrollTo(120, 0);

```

////////////////////////
### HashState
Hash part of url is changed.

```
$this->waxed->hashState('state/of/art');

```


////////////////////////
### PushState


////////////////////////

### Flush


```
$this->waxed->flush();

```

////////////////////////

### View


```
$this->waxed->view($id, $template);

```


////////////////////////

## Configuration

```
$config['waxed'] = [
  'chunked' => true,
  'engine' => 'mark2',
  'plugin_name' => 'waxxx',
  'design_route' => '/tests/login/design/',
  'plugin_route' => '/tests/login/inc/',
  'action_route' => '/tests/showcase/ajax/',
  'design_path' => APPPATH . 'views\\login\\',
  'plugin_path' => NULL,
  'action_path' => NULL,
  'action_prefix' => '_W_',
  'defaults' => [
    'route' => '/tests/showcase/ajax/',
  ],
];

$this->waxed->setup($config['waxed']);

```

#### chunked
Boolean. Designates, if ajax responses are returned in chunked mode or not.

#### engine
Selects mustache engine for templating.
(mark2|)

#### plugin_name


#### design_route
URL route, which will be called for HTML templates, images and CSS stylesheets.

#### plugin_route
URL route, which will be called for JS plugins.

#### action_route
URL route, which will be called for AJAX responses.

#### design_path
path, where templates are stored

#### plugin_path
path, where javascript plugins are stored

#### action_path
path, where additional controllers are stored

#### action_prefix

#### defaults array
array of values, which are appended to each display command.

////////////////////////

## Chunked
If Waxed is working in chunked mode, json responses are returned in
smaller chunks, which could be utilizied in browser immediatelly.
Thanks to that, it is possible to divide command in time:
we can for example immediatelly display progress bar,
before some time consuming iterations begins,
then periodically update progress bar during iterations,
and after all display the results: still inside the same HTTP reponse.

```

$data = [
  'eta' => 100,
];
$this->waxed->display($data, 'progress')->flush();
for ($i = 100; $i > 0; $i--) {
  $data['eta'] = $i;
  $this->waxed->display($data, 'progress')->flush();
  sleep(1000);
}
$this->waxed->display($data, 'results')->flush();

```
