<?php

namespace Waxedphp\Waxedphp\Scripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        $event->getIO()->write("do the stuff");
        // do stuff
    }

    public static function postPackageUpdate(PackageEvent $event)
    {
      $operation = $event->getOperation();
      $package = method_exists($operation, 'getPackage')
        ? $operation->getPackage()
        : $operation->getInitialPackage();

      $packageName = $package->getName();

      $event->getIO()->write('package: ' . $packageName);

      if (strpos($packageName, 'waxedphp/') === 0) {
        $event->getIO()->write("do the package stuff");
      };
        // do stuff
    }

}
