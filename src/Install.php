<?php

namespace Waxedphp\Waxedphp;

use Waxedphp\Waxedphp\Php\Dependency;
use Waxedphp\Waxedphp\Php\Config;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;

#npm install @picocss/pico

class Install {

  static function configure() {
    $cfg = new Config();
    print_r($cfg->getConfig());
  } 
  
  static function postUpdatex() {
    echo __FUNCTION__."\n";
    $dir = dirname(\Composer\InstalledVersions::getInstallPath('waxedphp/waxedphp'));
    $installed = \Composer\InstalledVersions::getInstalledPackages();
    foreach ($installed as $package) {
      //echo $package . "\n";
      if (strpos($package,'waxedphp/')===0) {
        $dep = new Dependency(null, substr($package,9));
        //echo substr($package,9) . "\n";
        print_r($dep->getPath() . "\n");
        print_r($dep->getInstallScript());
        echo "\n\n";
      };
    };
    
    $result = [];
    exec('npm ls --json', $result);
    print_r(json_decode(implode("\n",$result),true));    
    
    return;
    $cwd = getcwd();
    print_r($cwd);
    //@mkdir($dir . '/.data');
    //file_put_contents($dir . '/.data/installed.json', json_encode($ins));
    $result = [];
    exec('npm install @picocss/pico', $result);
    print_r($result);
  }


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

    public static function postPackageInstall(PackageEvent $event)
    {
      $operation = $event->getOperation();
      $io = $event->getIO();
      $installationManager = $event->getComposer()->getInstallationManager();

      $package = method_exists($operation, 'getPackage')
        ? $operation->getPackage()
        : $operation->getInitialPackage();

      $packageName = $package->getName();
      if (strpos($packageName, 'waxedphp/') === 0) {

        $targetDir = $package->getTargetDir();
        $originDir = $installationManager->getInstallPath($package);
        $io->write('package: ' . $packageName);
        $io->write('origin dir: ' . $originDir);

        $dep = new Dependency(null, substr($packageName,9));
        $dep->doInstallScript();

        $baseName = explode('/', $packageName)[1];
        if($baseName == 'waxedphp') $baseName = 'base';
        $io->write("do the package stuff " . $baseName);
        /*
        if (!is_dir($pluginDir . DIRECTORY_SEPARATOR . $baseName)) {
          symlink(
            $originDir . DIRECTORY_SEPARATOR . 'pkg',
            $pluginDir . DIRECTORY_SEPARATOR . $baseName
          );
        };
        */
        //exec('npm i -s plyr');
      };
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
        /*
        if (!is_dir($pluginDir . DIRECTORY_SEPARATOR . $baseName)) {
          symlink(
            $originDir . DIRECTORY_SEPARATOR . 'pkg',
            $pluginDir . DIRECTORY_SEPARATOR . $baseName
          );
        };
        */
        //exec('npm i -s plyr');
      };
      // do stuff
    }

    public static function postPackageUninstall(PackageEvent $event)
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
        /*
        if (is_link($dir)) {
          $io->write('removing ' . $dir . ' !');
          unlink($dir);
        }
        */
        //
      };
        // do stuff
    }  
  
}
