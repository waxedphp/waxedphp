<?php
namespace Waxedphp\Waxedphp\Php\Builders;

use Waxedphp\Waxedphp\Php\Config;
use Waxedphp\Waxedphp\Waxed;

class Webpack {

/*
npm install sass-loader sass webpack --save-dev
npm install postcss postcss-cli autoprefixer
npm install --save-dev mini-css-extract-plugin
npm install --save-dev postcss-loader postcss
npm install --save-dev babel-loader @babel/core
npm install @babel/preset-env --save-dev
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
    $cmd = 'npx webpack ';
    $cmd.= '--progress ';
    $cmd.= '--config "' . $this->makeConfig() . '" ';
    $cmd.= '--entry-reset ';
    $cmd.= '--entry "' . $assets . '/' . $usage . '/loader.js" ';
    $cmd.= '--output-path="' . $assets . '/' . $usage . '/" ';
    $re = [];
    exec($cmd, $re);
    unlink($assets . '/' . $usage . '/loader.js');
    print_r($re);
  }

  public function makeConfig():string {
    $f = $this->config->getDataDir() . '/webpack.config.cjs';
    $b = $this->config->getDataDir() . '/myBabelPreset';
    //if (is_file($f)) return $f;
    $cfg = $this->config->getConfig();
    $s = '' . "\n";
    $s.= "const path = require('path');\n";
    $s.= "const webpack = require('webpack');\n";
    $s.= "const TerserPlugin = require('terser-webpack-plugin');\n";
    $s.= "const MiniCssExtractPlugin = require('mini-css-extract-plugin');\n";
    $s.= "\n";
    $s.= "module.exports = {\n";
    $s.= "  resolve: {\n";
    //$s.= "    modules: [path.resolve(__dirname, '../node_modules'), 'node_modules']\n";
    $s.= "    modules: ['" . $cfg['nodejs_path'] . "', 'node_modules']\n";
    $s.= "  },\n";
    $s.= "  module: {\n";
    $s.= "    rules: [\n";
    $s.= "      {\n";
    $s.= '      test: /\.css$/i,'."\n";
    $s.= "      use: [{\n";
    $s.= "        loader: MiniCssExtractPlugin.loader\n";
    $s.= "      }, {\n";
    $s.= "        loader: 'css-loader'\n";
    $s.= "      }]\n";
    $s.= "      }, {\n";
    $s.= '      test: /\.(scss)$/,'."\n";
    $s.= "      use: [{\n";
    $s.= "          loader: MiniCssExtractPlugin.loader\n";
    $s.= "        }, {\n";
    $s.= "          loader: 'css-loader'\n";
    $s.= "        }, {\n";
    $s.= "          loader: 'postcss-loader',\n";
    $s.= "          options: {\n";
    $s.= "          }\n";
    $s.= "        }, {\n";
    $s.= "          loader: 'sass-loader'\n";
    $s.= "        }]\n";
    $s.= "      },\n";

    $s.= "      {\n";
    //$s.= '          test: /\.m?js$/,'."\n";
    $s.= '          test: /\.(?:js|mjs|cjs)$/,'."\n";
    $s.= "          exclude: /node_modules/,\n";
    $s.= "          use: {\n";
    $s.= "            loader: 'babel-loader',\n";
    $s.= "            options: {\n";
    $s.= "              targets: 'defaults',\n";//added !!!
    //$s.= "              presets: ['".$b."' ,'@babel/preset-env']\n";
    $s.= "              presets: ['@babel/preset-env']\n";
    $s.= "            }\n";
    $s.= "          }\n";
    $s.= "      }\n";

    $s.= "    ]},\n";
    $s.= "  optimization: {\n";
    $s.= "    minimizer: [new TerserPlugin({\n";
    $s.= "      extractComments: false,\n";
    $s.= "    })],\n";
    $s.= "  },\n";
    $s.= "  plugins: [\n";
    $s.= "    new webpack.BannerPlugin(new Date().toString()),\n";
    $s.= "    new MiniCssExtractPlugin(),\n";
    $s.= "    new webpack.ProvidePlugin({\n";
    $s.= '      $: "jquery",'."\n";
    $s.= '      jQuery: "jquery"'."\n";
    //$s.= '      "window.jQuery": "jquery"'."\n";
    $s.= "    })\n";
    $s.= "  ]\n";
    $s.= "  };\n";
    $s.= "\n";
    file_put_contents($f, $s);
    return $f;
  }

}
