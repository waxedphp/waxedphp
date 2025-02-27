## Charts

Lets say, you need charts. Managers love charts. Easy as this:

```php
  $waxed->pick('main')->display([
    'data' => $this->waxed->plugin->get_example_data('apexcharts'),
  ], 'apexchartsTemplate');
      

```
