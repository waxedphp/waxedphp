
```

composer require waxedphp/waxedphp
composer require --dev waxedphp/waxedphpdev
composer require waxedphp/jsonviewer
composer require waxedphp/apexcharts


```

```

cd vendor/waxedphp/apexcharts/
bash install.sh


```



```

WAXED.development=true
WAXED.writable_path='../waxed/assets/'
WAXED.package_path='../waxed/packages/'
WAXED.plugin_route='/plugins/'

WAXED.action_route='/ajax'
WAXED.design_route='/html/'
WAXED.design_path='html'

WAXED.nodejs_path='../node_modules/'


```


## LARAVEL:


create app/Providers/WaxedServiceProvider.php :

```

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use \Waxedphp\Waxedphp\Waxed as Waxed;

/**
 * After changing singleton class, run:
 * composer dumpauto
 *
 */

class WaxedServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Waxed::class, function (Application $app) {
            $waxed = new Waxed();
            $cfg = [
                'action_route' => '/ajax',
                'plugin_route' => '/plugins/',
                'design_route' => '/html/',
                'design_path' => 'html',
                'writable_path' => '../waxed/assets/',
                'package_path' => '../waxed/packages/',
                'development' => false,
                'nodejs_path' => false,
            ];
            foreach ($cfg as $key => $val) {
              $cfg[$key] = env('WAXED.' . $key, $val);
            };
            $waxed->setup($cfg);
            return $waxed;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [Waxed::class];
    }

}

```

into config/app.php add:
```

    'providers' => ServiceProvider::defaultProviders()->merge([
        ...
        App\Providers\WaxedServiceProvider::class,
    ])->toArray(),

```

