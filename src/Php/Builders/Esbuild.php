<?php
namespace Waxedphp\Waxedphp\Php\Builders;

use Waxedphp\Waxedphp\Php\Config;
use Waxedphp\Waxedphp\Waxed;

class Esbuild {

/*
npm install --save-exact --save-dev esbuild
//npm install esbuild-plugin-svg -D
//npm install esbuild-plugin-ignore
//npm install esbuild-plugin-external-global
//npm install --save-dev esbuild-plugin-globals
*/

  private ?Config $config = null;

  public function __construct() {
    $this->config = new Config();
  }

  public function allowBuild(string $usage) {
    $cfg = $this->config->getConfig();
    $assets = $cfg['writable_path'];
    if (!is_dir($assets)) mkdir($assets);
    if (!is_dir($assets . '/' . $usage)) mkdir($assets . '/' . $usage);
    chmod($assets . '/' . $usage, 0777);
  }

  public function build(string $usage) {
    $waxed = new Waxed();
    $waxed->route('/')->configure()->setDevelopment(true);
    $waxed->plugin->uses(explode('-', $usage))->prepare_build();
    $cfg = $this->config->getConfig();
    $assets = $cfg['writable_path'];
    if (!is_dir($assets)) mkdir($assets);
    if (!is_dir($assets . '/' . $usage)) mkdir($assets . '/' . $usage);


    $cmd = 'NODE_PATH="' . $cfg['nodejs_path'] . '" ';

    if (6==6) {
      $outdir = $assets . '/' . $usage;

      //https://github.com/evanw/esbuild/issues/399
      $globaljs = $this->makeGlobalsEntryPoint($outdir);
      $entryPoints = [
          $globaljs,
          $outdir . '/loader.js',
      ];


      $cmd .= 'node ' . $this->makeConfig(
        $entryPoints,
        $outdir,
        $cfg
      );

    } else {



      $cmd .= 'npx esbuild ';

      $cmd.= '--bundle "' . $assets . '/' . $usage . '/loader.js" ';
      $cmd.= '--outfile="' . $assets . '/' . $usage . '/main.js" ';


      //$cmd.= '--minify ';
      $cmd.= '--sourcemap ';
      //$cmd.= '--format=esm ';
      $cmd.= '--loader:.svg=text ';
      $cmd.= '--loader:.png=binary ';
      $cmd.= '--loader:.woff2=binary ';
      $cmd.= '--platform=browser ';
      $cmd.= '--reserve-props=$,window.$,jQuery ';
      $cmd.= '--target=firefox57 ';//chrome58,firefox57,safari11,edge16
    }
    $re = [];
    exec($cmd, $re);
    //unlink($assets . '/' . $usage . '/loader.js');

    $s = '';
    foreach ($entryPoints as $entryPoint) {
      if (is_file($entryPoint)) {
        $s .= file_get_contents($entryPoint);
        unlink($entryPoint);
      }
    }
    //$s .= 'console.log(\'LODASH: \',_);'."\n";
    file_put_contents($assets . '/' . $usage . '/main.js', $s);
    if (is_file($outdir . '/loader.css')) {
      rename($outdir . '/loader.css', $outdir . '/main.css');
    };


    print_r($re);
  }

  public function makeConfig($entryPoints, $outdir, $cfg):string {
    $f = $this->config->getDataDir() . '/esbuild.config.mjs';
    $b = $this->config->getDataDir() . '/myBabelPreset';

    //if (is_file($f)) return $f;
    //$cfg = $this->config->getConfig();
    $s = '' . "\n";
    $s.= "import * as esbuild from 'esbuild';\n";

    //$s.= "import externalGlobalPlugin from 'esbuild-plugin-external-global';\n";
    //$s.= "import { globalExternals } from \"@fal-works/esbuild-plugin-global-externals\";\n";

    /*
    $s.= "import GlobalsPlugin from \"esbuild-plugin-globals\";\n";
    $s.= "const globals = {\n";
    $s.= "  _: \"lodash\",\n";
    $s.= "  $: \"jquery\",\n";
    $s.= "  jQuery: \"jquery\"\n";
    $s.= "};\n";
    */

    //$s.= "const esbuild = require(\"esbuild\");\n";
    //$s.= "const { externalGlobalPlugin } = require(\"esbuild-plugin-external-global\");\n";


    $s.= "\n";
    //$s.= "console.log(externalGlobalPlugin);\n";
    $s.= "await esbuild.build({\n";
    //$s.= "esbuild.build({\n";

    $s.= "entryPoints: [\n";
    //$s.= "'" . $entryPoint . "'
    if (is_array($entryPoints)) {
      $ss = '';
      foreach ($entryPoints as $entryPoint) {
        if ($ss) $ss.=',';
        $ss.= "'" . $entryPoint . "'\n";
      };
      $s.=$ss;
    } else if (is_string($entryPoints)) {
      $s.= "'" . $entryPoints . "',\n";
    }

    $s.= "],\n";
    $s.= "nodePaths: ['" . $cfg['nodejs_path'] . "'],\n";
    $s.= "target: [\n";
    $s.= "  'chrome58',\n";
    $s.= "  'firefox57',\n";
    $s.= "  'edge18',\n";
    $s.= "  'safari11',\n";
    $s.= "  'opera38'\n";
    $s.= "],\n";
    $s.= "bundle: true,\n";
    $s.= "keepNames: true,\n";
    $s.= "platform: 'browser',\n";
    $s.= "loader: {\n";
    $s.= "  '.woff2': 'binary',\n";
    $s.= "  '.png': 'binary',\n";
    $s.= "  '.svg': 'text'\n";
    $s.= "},\n";


    $s.= "plugins: [\n";
    //$s.= "  globalExternals(globals)\n";
    //$s.= "  GlobalsPlugin(globals)\n";
    //$s.= "  externalGlobalPlugin({\n";
    //$s.= "  'react': 'window.React',\n";
    //$s.= "  'react-dom': 'window.ReactDOM',\n";
    //$s.= "  'jQuery': '$',\n";
    //$s.= "  '$': '$',\n";
    //$s.= "  'fetch-json': 'globalThis'\n";
    //$s.= "})\n";
    $s.= "],\n";

    //$s.= "outfile: '" . $outdir . "/main.js',\n";
    $s.= "allowOverwrite: true,\n";
    $s.= "minify: true,\n";
    $s.= "outdir: '" . $outdir . "/',\n";

    $s.= "});\n";

    $s.= "\n";
    file_put_contents($f, $s);
    chmod($f, 0777);
    return $f;
  }

  /**
   *
   * https://github.com/evanw/esbuild/issues/399
   */

  public function makeGlobalsEntryPoint($outdir) {
    $fname = $outdir . '/loader-0.js';
    $s = "\n";
    $s.= "import jQuery from 'jquery4';\n";
    //$s.= "console.log(jQuery);\n";
    $s.= "\n";
    //$s.= "window.jQuery = jQuery;\n";
    //$s.= "window.$ = $ = jQuery;\n";

    $s.= "globalThis.$ = jQuery;\n";
    $s.= "globalThis.jQuery = jQuery;\n";

    $s.= "\n";
    $s.= "globalThis._ = require('lodash');\n";
    file_put_contents($fname, $s);
    chmod($fname, 0777);
    return $fname;
  }

}
