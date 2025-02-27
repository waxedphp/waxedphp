
////////////////////////
## Quick Start:

```

composer require waxedphp/waxedphp


```


Inside composer.json - add post-update-cmd, post-package-install, post-package-update, post-package-uninstall commands:

```
    "scripts": {
        "test": "phpunit",
        "post-update-cmd": "Waxedphp\\Waxedphp\\Install::postUpdate",
        "post-package-install": "Waxedphp\\Waxedphp\\Install::postPackageInstall",
        "post-package-update": "Waxedphp\\Waxedphp\\Install::postPackageUpdate",
        "post-package-uninstall": "Waxedphp\\Waxedphp\\Install::postPackageUninstall"
    }
```

```

composer exec "wax --development"


```

Write config:

```
  "waxed": {
    "chunked": true,
    "engine": "mark2",
    "design_route": "/waxed/design/",
    "design_path": "/var/www/public/views",
    "plugin_route": "/waxed/plugin/"
  }

```

Prepare service controller:
```

class WaxedController extends ControllerBase
{
    public function beforeExecuteRoute()
    {
      $conf = DI::getDefault()->get('config');
      $this->waxed = new \Waxed();
      $this->waxed->setup($conf->waxed);
    }

    public function pluginAction()
    {
      $a = implode('/', func_get_args());
      $this->waxed->plugin->dispatch($a);
    }

    public function designAction()
    {
      $a = implode('/', func_get_args());
      $this->waxed->design->dispatch($a);
    }

}

```

Prepare main application controller:
```

  public function indexAction() {

    $this->waxed->pick('main')->display([

    ], 'hello');

    $this->waxed->view('main', 'index');
  }


```

Prepare controller method for ajax requests:
```

  public function ajaxAction() {
    switch ($_POST['action']) {
      case 'dialog/login':
        $this->waxed->pick('main')->dialog([

        ], 'login');
        break;
      case 'login/sent':
        try {
          $this->USER_MODEL->login($_POST['login'], $_POST['password']);
        catch (Exception $e) {
          $this->waxed->pick('dialog')->invalidate($e)->flush();
          exit;
        }
        $this->waxed->reload();
      break;
      default:
        $this->waxed->pick('main')->display([

        ], 'hello');
        break;
    }
    $this->waxed->flush();
  }


```

Prepare HTML templates:

```
<!-- hello.html -->
<h1>Hello!</h1>
<p>
  Dont you want to
  <a class="waxed-action" data-action="dialog/login" href="{{route}}" >log in</a>
  ?
</p>

```


```
<!-- login.html -->
<form method="post" action="{{route}}" >
  <label>login:</label>
  <input type="text" name="login" />
  <label>password:</label>
  <input type="password" name="password" />
  <input type="hidden" name="action" value="login/sent" />
  <input type="submit" value="OK" >
</form>


```


Done!

////////////////////////

