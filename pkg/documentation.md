# Waxedphp

Framework for rapid web frontend development, waxed as mustache.

Is a toolbox which allows PHP developer to easily prototype rich interactive web applications without too much fiddling with javascript and css.
Contains curated and growing collection of frontend solutions for many typical scenarios - all driven from PHP code.

MIT License
---

### PHP:

```

$this->waxed->display([
  'title' => 'Hey you, click here...',
  'action' => 'do/the/wax',
], 'template');
$this->waxed->flush();


```

After clicking on the link, this is what SERVER will do:

```

if ($_POST['action'] == 'do/the/wax') {
  $this->waxed->dialog([
    'selected' => implode(', ', $_POST),
  ], 'debug');
  $this->waxed->flush();
};

```
---
### HTML:

```

<div class="boxload" data-url="{{route}}" ></div>

<a class="action"
  href="{{route}}"
  data-action="{{action}}" >{{title}}</a>


```
---
---


# browser side of WAX

## Idea Behind:

Virtual DOM is not needed here.
The changes are triggered either by user interaction or from the server.

There are two complementary approaches combined here together:
RENDERING the template (with "mustache" syntax, or other similar templating technique)
and afterwards
VITALIZE selected elements with their own autonomous logic.

### Root Element
Root element could be either the whole visible part of page, or, which is preferable,
there could be multiple root elements on the page.
Each root element could be for example connected to other url, maintain his own set of elements, logic etc.
If you narrow the space of root element, operations are faster, as WAXED works only on small set of elements.
Just remember, that root elements should not overlap.

### Template Syntax
Templates are written in plain HTML & Mustache.
All important page functionality should be captured here in the declarative manner.
The implementation then depends on multiple resources: CSS, JS, images, possibly others.
The inputs are composed of interaction between USER and SERVER.
Data to the template are injected from the json tree.
Rendering engine cares of variables, lists, trees.
Loops through the lists and draws for example rows of table, menu structures, etc.
There is plenty of rendering engines for javascript available - with proper sleeve you can use any of them.
I prefer "mustache" type of syntax - because it is mature and has good implementation in nearly all possible computer languages,
namely JS, PHP, PYTHON, RUBY... Available engines with "mustache" syntax are for example:
"Hogan" (used by Twitter), "Handlebars". I also developed my own engine, called "Mark2".
The biggest thing about mustache templates:
The same template file could be rendered server side as well as browser side.

### Waxed Plugin
When the template is rendered in the root element, user could see, what we prepared for him.
But there is not much interaction yet, except of good old plain A, FORM, BUTTON tags.
Now its time for Waxed and his plugins to do the work. In the scope of root element,
Waxed walks over dom structure to see, if there are some selectors known to him.

Waxed itself recognizes only few of selectors:

 Selector | Exception | Result
 --- | --- | ---
 a[data-action="*"] | - |(triggers behavior "load" when clicked)
 button[data-action="*"] | - | (triggers behavior "load" when clicked)
 form[data-action="*"] | form.ownLogic | (triggers behavior "load" when submitted)

Matching elements are from this moment handled by Waxed.

### Waxed SubPlugin
But there are other SubPlugins, and they has their own selectors.

For example:

| Selector | Plugin | Result |
| -------- | ------ | ------ |
| textarea.waxed-ckeditor | ckeditor | (wysiwyg editor is built inside textarea, and its API is connected with Waxed) |
| table.waxed-datatable | datatables | (advanced interaction controls for table are built, and its API is connected with Waxed) |
| select.waxed-selectize | selectize | (advanced dropdown element is built, and its API is connected with Waxed) |

Matching elements are from this moment handled by their own plugins - classes.
Theese plugins are further governed by Waxed.
The plugin must be loaded in the moment of rendering.
Waxed manages also loading of this plugins - and therefore it is possible to do such sequence of commands:
Show waiting flag - Load plugins - Render template - Hide waiting flag.

### Behaviors
The inputs for web application are composed from interactions between USER and SERVER.
USER is clicking, dragging, dropping, pushing, keystroking...
On defined events, the SERVER is connected, through the Waxed mechanism.
SERVER responds with command, or rather sequence of commands to the Waxed, and Waxed achieve.
Thanks to a Waxed, there is a huge palette of commands, so called "behaviors" at hand.
It is also possible to define behaviors, which doesnt connect SERVER, and do their work only browser side.

#### Actions
Behaviors triggered by USER are called in this scope "Actions".
In the HTML, they are represented as data-action parameter.
Change of hash part of URL also triggers action.

#### Commands
Behaviors triggered by SERVER are called in this scope "Commands".

## Vocabulary:

1. Root element

2. Template

3. Waxed element

4. Pluggable

5. Plugins

6. Plugin instances

7. SuperPlug

8. Sleeve
Facade for the common javascript or jquery plugin.
Sleeve glues existing plugin together with Waxed.
It is achieved mainly by theese generic methods:
- init - initialize plugin from declarative HTML and from within data tree
- setRecord - this method injects the new set of data inside existing object
- invalidate - if object could send some invalid data, this method pursues to mark errorneous input for user
- free - pursues to free resources before the associated object is removed from dom structure
- api - maintains other api calls

## Built-in Behaviors:

There is a handfull of "behaviors" already built in Waxed.js
Most of them has the counterparts with the same name in Waxed php library.

1. display
   > Load the template, render it with new dataset inside element.
   > Then enlive Waxed elements inside this template with dataset.

2. dialog
   > Open dialog panel, load the template, render it with new dataset inside this panel.
   > Then enlive Waxed elements inside this template with dataset.
   > This command has a bunch of more specific variants:
   >
   > 1. dialog/open
   > 2. dialog/close
   > 3. dialog/modal
   > 4. dialog/free

3. inspire
   > Dont load the template, just find and enlive Waxed elements in existing dom structure with new data.

4. redraw
   > Similar as "inspire", but only applies on existing Waxed elements, without initialization.

5. title
   > Change page title in browser.

6. favicon
   > Change page favicon in browser.

7. submit
   > Submit selected form.

8. invalidate
   > Show error flags or labels in forms and Waxed elements.

10. hide
    > Hide selected element.

11. show
    > Show selected element.

12. clear
    > Clears selected element.

13. log
    > Console log - for debugging purposes only.

14. assign
    > Through this command it is possible to push named html templates inside templating engine.

15. ckeditor/file/callback  [DEPRECATED]
    > Specific behavior for plugin "ckeditor"; should be removed from Waxed root.

16. iframe/callback  [DEPRECATED]
    > Connects with parent window from within iframe on same domain;
    > Should be removed from Waxed root and moved to own plugin.

17. keydown/load  [DEPRECATED]
    > Installs keyboard shortcuts for the page.
    > Should be removed from Waxed root and moved to own plugin.

18. hashState
    > Change hash of url. Doesnt triggers hash change behavior (loadState).

19. pushState


20. loadState
    > Connects the server after the hash was changed and expects set of commands in response.
    > (Datas are provided as classic POST (or GET) parameters, while response is in JSON form.)

21. load
    > Connects the server with the defined datas and expects set of commands in response.
    > (Datas are provided as classic POST (or GET) parameters, while response is in JSON form.)
    >
    > 1. ontime/load [DEPRECATED]

22. behave
    > Installs additional behaviors for the page.

23. scrollTo
    > Scroll page to X, Y position. X could be ommited for vertical scrolling only.

24. scrollTop
    > Scrolls page to the top.

25. command
    > This command allows us to address command to the selected Waxed element api.

26. gourl
    > Redirects browser to another url.

27. reload
    > Reloads page in browser.

28. window/open
    > Browser should open another page. This could be denied by browser.

29. fullscreen
   > Force fullscreen mode in browser.

30. loadPlugins  [DEPRECATED]
   > Force to load and initialize plugins, which was not on the page before.
   > As this is called automatically in the end of "plug" command, this should probably be removed from Waxed triggers.

31. plug
    > Page will load list of JS and CSS files. Waxed plugins will be connected within Waxed main controller.

32. func
    > Function stored in parameter is executed.
    > This command is intended to work only within browser side application, as server couldnt send function type (only as string).

33. loadTemplateBlock
    > Templating engine takes the template from within the existing element on page.
    > Id of element is required.

34. setBatch  [DEPRECATED]
    > Sets batch name.
    > Should be removed from Waxed root and moved to own plugin.

35. stopBatch  [DEPRECATED]
    > Stops batch loop.
    > Should be removed from Waxed root and moved to own plugin.

36. multi
    > This command serves as envelope for multiple commands, as one server connection usually wants to trigger more of them.

## Plugin Behaviors:
Each plugin can define its own set of behaviors.

## Defined Behaviors:
SERVER could define its own set of behaviors.
It is also possible to create mechanism for defininig behaviors by USER,
for example in form of some terminal window, but there was no reason for such feature yet.

## Essential SubPlugins:

There are two kind of subplugins, which are essential to Waxed, and therefore some of them must be loaded.
You can choose, which subplugin best fits for your application.

### Template Engine
1. mark2
   Recommended.

2. Could be replaced with:
   hogan, handlebars, nunjucks etc...

### Dialog Window
1. tingle
   Recommended.

2. Could be replaced with:
   bs_modal_single, bs_modal_multi, facebox, multimodal...

## Bootstrap Integration Plugins:

bs_dropdown

bs_progressbar

bs_window

## Other Notable Plugins:

ace

animate

autocomplete

autoselect

awesome

blockui

chartjs

ckeditor

codemirror

datatables

datetimepicker

dependson

hamburger

interact

jqtree

longpolling

magicsuggest

matrix

notify

selectize

sortable

tabslet

tagit

toggles

tooltips

websocket






