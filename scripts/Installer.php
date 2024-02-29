<?php

namespace Waxedphp\Waxedphp\Scripts;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
    public static function postUpdate(Event $event)
    {
      $composer = $event->getComposer();
      $installationManager = $event->getComposer()->getInstallationManager();
      $io = $event->getIO();

      $packages = $event->getComposer()->getRepositoryManager()
          ->getLocalRepository()->getPackages();
      foreach ($packages as $package) {
        $packageName = $package->getName();
        $installPath = $installationManager->getInstallPath($package);
        if (strpos($packageName, 'waxedphp/') === 0) {
          $baseName = explode('/', $packageName)[1];
          $io->write($installPath);
          $io->write($packageName);
        }
      }
      $io->write("do the stuff");

        // do stuff
    }

    public static function postPackageUpdate(PackageEvent $event)
    {
      $operation = $event->getOperation();
      $io = $event->getIO();
      $installationManager = $event->getComposer()->getInstallationManager();

      $package = method_exists($operation, 'getPackage')
        ? $operation->getPackage()
        : $operation->getInitialPackage();

      $packageName = $package->getName();
      $targetDir = $package->getTargetDir();
      $originDir = $installationManager->getInstallPath($package);

      $pluginDir = getenv('WAXEDPHP');

      //symlink($target, $link);

      $io->write('package: ' . $packageName);
      $io->write('origin dir: ' . $originDir);
      if (strpos($packageName, 'waxedphp/') === 0) {
        $baseName = explode('/', $packageName)[1];
        if($baseName == 'waxedphp') $baseName = 'base';
        $io->write("do the package stuff " . $baseName);
        if (!is_dir($pluginDir . DIRECTORY_SEPARATOR . $baseName)) {
          symlink(
            $originDir . DIRECTORY_SEPARATOR . 'pkg',
            $pluginDir . DIRECTORY_SEPARATOR . $baseName
          );
        };
        //exec('npm i -s plyr');
      };
      // do stuff
    }

    public static function postPackageRemove(PackageEvent $event)
    {
      $operation = $event->getOperation();
      $io = $event->getIO();
      $installationManager = $event->getComposer()->getInstallationManager();

      $package = method_exists($operation, 'getPackage')
        ? $operation->getPackage()
        : $operation->getInitialPackage();

      $packageName = $package->getName();
      $targetDir = $package->getTargetDir();
      $originDir = $installationManager->getInstallPath($package);

      $pluginDir = getenv('WAXEDPHP');

      //symlink($target, $link);

      $io->write('package: ' . $packageName);
      $io->write('origin dir: ' . $originDir);
      if (strpos($packageName, 'waxedphp/') === 0) {
        $baseName = explode('/', $packageName)[1];
        if($baseName == 'waxedphp') $baseName = 'base';
        $io->write("do the package stuff " . $baseName);
        $dir = $pluginDir . DIRECTORY_SEPARATOR . $baseName;
        $io->write($dir);
        if (is_link($dir)) {
          $io->write('removing ' . $dir . ' !');
          unlink($dir);
        }
        //
      };
        // do stuff
    }

}
