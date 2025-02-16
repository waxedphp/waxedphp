
/*
npm install sass-loader sass webpack --save-dev
npm install postcss postcss-cli autoprefixer
npm install --save-dev mini-css-extract-plugin
npm install --save-dev postcss-loader postcss
* 
 * 
cd "/home/andy/web/waxed/src/pkg"
webpack --config webpack.config.js --entry=/home/andy/web/waxed/src/pkg/loader.js --output-path="/home/andy/web/andybezak.eu/web/assets/" --output-filename="base-ace-asteroids-bootstrap-jsonviewer-tingle.js"
 */


const path = require('path');
const webpack = require("webpack");
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  module: {
    rules: [
      {
        test: /\.css$/i,
        //use: ["style-loader", "css-loader"],
        use: [{
          //loader: 'style-loader', // inject CSS to page
          loader: MiniCssExtractPlugin.loader
        }, {
          loader: 'css-loader', // translates CSS into CommonJS modules
        }]
      },
      {
        test: /\.(scss)$/,
        use: [{
          //loader: 'style-loader', // inject CSS to page
          loader: MiniCssExtractPlugin.loader
        }, {
          loader: 'css-loader', // translates CSS into CommonJS modules
        }, {
          loader: 'postcss-loader', // Run post css actions
          options: {
          }
        }, {
          loader: 'sass-loader' // compiles Sass to CSS
        }]
      }      
      
    ],
  },
  /*
  entry: './loader.js',
  output: {
    filename: 'base-ace-asteroids-bootstrap-jsonviewer-tingle.js',
    path:'/home/andy/web/andybezak.eu/web/assets/'
  },
  */
  optimization: {
    minimizer: [new TerserPlugin({
      extractComments: false,
    })],
  },
  plugins: [
    new webpack.BannerPlugin(new Date().toString()),
    new MiniCssExtractPlugin(),
    new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery"
    })
  ]
};
