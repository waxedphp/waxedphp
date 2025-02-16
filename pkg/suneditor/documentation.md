# SunEditor

Suneditor is a lightweight, flexible, customizable WYSIWYG text editor for your web applications.

MIT license

http://suneditor.com/sample/index.html

### HTML:

```

<textarea class="waxed-suneditor"
  data-name="payload1"
></textarea>

```

### PHP:

```

$this->waxed->display([
  'payload' =>
    [
      'value' => 'program Test;
      uses MyLib;
      begin
        writeln(\'Hello world.\')
      end.
      ',
      'mode' => 'ace/mode/pascal',
      'theme' => 'ace/theme/tomorrow',
    ],
], 'template');

```


