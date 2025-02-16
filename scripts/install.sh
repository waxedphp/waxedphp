#!/bin/bash
# bash vendor/jasterstary/waxed/scripts/install.sh
cd "$(dirname "$0")";
cd ..
PA=`pwd`
cd ../../../;
#pwd
#echo $PA
#exit;
PUBLIC="public";

mkdir -p webpack/assets
mkdir -p $PUBLIC/html
mkdir -p $PUBLIC/assets
chmod ugo+rwx webpack/assets
if ! [ -f webpack/build.sh ]; then
  cp $PA/webpack/build.sh webpack/
  cp $PA/webpack/webpack.config.cjs webpack/
  cp $PA/webpack/assets/* webpack/assets/
fi
if ! [ -f $PUBLIC/html/index.html ]; then
  cp $PA/webpack/html/index.html  $PUBLIC/html/index.html
fi
if ! [ -f $PUBLIC/html/dump.html ]; then
  cp $PA/webpack/html/dump.html  $PUBLIC/html/dump.html
fi
