import ace from 'ace-builds/src-noconflict/ace.js';

import ext from 'ace-builds/src-noconflict/ext-language_tools.js';
import extSearch from 'ace-builds/src-noconflict/ext-searchbox.js';

ace.define('ace/ext/language_tools', null, ext);
ace.define('ace/ext/searchbox', null, extSearch);

ace.config.set("basePath", "/assets/ace/");

import "./plugin.js";
