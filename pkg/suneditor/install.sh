#!/bin/bash

npm install suneditor --save
npm i codemirror

mkdir 'sun-editor-build';
webpack --config ../webpack.config.js --entry="./build.js" --output-path="./sun-editor-build/"

