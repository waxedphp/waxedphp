#!/bin/bash

mkdir "$1"
cd "$1"

TARGET_FILE=dependency.php
if [ -f "$TARGET_FILE" ]
then
    echo "$TARGET_FILE exists."
else
    echo "$TARGET_FILE does not exist."
    echo "<?php" > $TARGET_FILE
    echo "" >> $TARGET_FILE
    echo "return [" >> $TARGET_FILE
    echo "  'js' => [" >> $TARGET_FILE
    echo "    '/$1/plugin.js'," >> $TARGET_FILE
    echo "  ]," >> $TARGET_FILE
    echo "  'css' => [" >> $TARGET_FILE
    echo "    '/$1/style.css'," >> $TARGET_FILE
    echo "  ]," >> $TARGET_FILE
    echo "];" >> $TARGET_FILE
    echo "" >> $TARGET_FILE
fi


TARGET_FILE=documentation.md
if [ -f "$TARGET_FILE" ]
then
    echo "$TARGET_FILE exists."
else
    echo "$TARGET_FILE does not exist."
    echo "# $1" > $TARGET_FILE
    echo "" >> $TARGET_FILE
    echo "MIT license" >> $TARGET_FILE
    echo "" >> $TARGET_FILE
    echo "" >> $TARGET_FILE
    echo "### HTML:" >> $TARGET_FILE
    echo "" >> $TARGET_FILE
    echo "\`\`\`" >> $TARGET_FILE
    echo "" >> $TARGET_FILE
    echo "\`\`\`" >> $TARGET_FILE
    echo " " >> $TARGET_FILE
    echo "### PHP:" >> $TARGET_FILE 
    echo " " >> $TARGET_FILE 
    echo "\`\`\`" >> $TARGET_FILE 
    echo " " >> $TARGET_FILE 
    echo "\`\`\`" >> $TARGET_FILE 
    echo " " >> $TARGET_FILE 
fi

if [ -f "plugin.js" ]
then
    echo "plugin.js exists."
else
  sed "s/boilerplate/$1/g" ../boilerplate/plugin.js > ./plugin.js
  sed "s/boilerplate/$1/g" ../boilerplate/style.css > ./style.css
fi
