
;(function ( $, window, document, undefined ) {

    var pluginName = 'ace',
        _search = 'textarea.waxed-ace',
        _api = ['getSelection'],
        defaults = {
            propertyName: "value"
        },
        inited = false,
        themeData = [
          ["Chrome", "chrome", "light"],
          ["Clouds", "clouds", "light"],
          ["Crimson Editor", "crimson_editor", "light"],
          ["Dawn", "dawn", "light"],
          ["Dreamweaver", "dreamweaver", "light"],
          ["Eclipse", "eclipse", "light"],
          ["GitHub", "github", "light"],
          ["IPlastic", "iplastic", "light"],
          ["Solarized Light", "solarized_light", "light"],
          ["TextMate", "textmate", "light"],
          ["Tomorrow", "tomorrow", "light"],
          ["XCode", "xcode", "light"],
          ["Kuroir", "kuroir", "light"],
          ["KatzenMilch", "katzenmilch", "light"],
          ["SQL Server"           ,"sqlserver"               , "light"],
          ["Ambiance"             ,"ambiance"                ,  "dark"],
          ["Chaos"                ,"chaos"                   ,  "dark"],
          ["Clouds Midnight"      ,"clouds_midnight"         ,  "dark"],
          ["Cobalt"               ,"cobalt"                  ,  "dark"],
          ["Gruvbox"              ,"gruvbox"                 ,  "dark"],
          ["Green on Black"       ,"gob"                     ,  "dark"],
          ["idle Fingers"         ,"idle_fingers"            ,  "dark"],
          ["krTheme"              ,"kr_theme"                ,  "dark"],
          ["Merbivore"            ,"merbivore"               ,  "dark"],
          ["Merbivore Soft"       ,"merbivore_soft"          ,  "dark"],
          ["Mono Industrial"      ,"mono_industrial"         ,  "dark"],
          ["Monokai"              ,"monokai"                 ,  "dark"],
          ["Pastel on dark"       ,"pastel_on_dark"          ,  "dark"],
          ["Solarized Dark"       ,"solarized_dark"          ,  "dark"],
          ["Terminal"             ,"terminal"                ,  "dark"],
          ["Tomorrow Night"       ,"tomorrow_night"          ,  "dark"],
          ["Tomorrow Night Blue"  ,"tomorrow_night_blue"     ,  "dark"],
          ["Tomorrow Night Bright","tomorrow_night_bright"   ,  "dark"],
          ["Tomorrow Night 80s"   ,"tomorrow_night_eighties" ,  "dark"],
          ["Twilight"             ,"twilight"                ,  "dark"],
          ["Vibrant Ink"          ,"vibrant_ink"             ,  "dark"]
        ],
        supportedModes = {
          ABAP:        ["abap"],
          ABC:         ["abc"],
          ActionScript:["as"],
          ADA:         ["ada|adb"],
          Apache_Conf: ["^htaccess|^htgroups|^htpasswd|^conf|htaccess|htgroups|htpasswd"],
          AsciiDoc:    ["asciidoc|adoc"],
          Assembly_x86:["asm|a"],
          AutoHotKey:  ["ahk"],
          BatchFile:   ["bat|cmd"],
          Bro:         ["bro"],
          C_Cpp:       ["cpp|c|cc|cxx|h|hh|hpp|ino"],
          C9Search:    ["c9search_results"],
          Cirru:       ["cirru|cr"],
          Clojure:     ["clj|cljs"],
          Cobol:       ["CBL|COB"],
          coffee:      ["coffee|cf|cson|^Cakefile"],
          ColdFusion:  ["cfm"],
          CSharp:      ["cs"],
          CSS:         ["css"],
          Curly:       ["curly"],
          D:           ["d|di"],
          Dart:        ["dart"],
          Diff:        ["diff|patch"],
          Dockerfile:  ["^Dockerfile"],
          Dot:         ["dot"],
          Drools:      ["drl"],
          Dummy:       ["dummy"],
          DummySyntax: ["dummy"],
          Eiffel:      ["e|ge"],
          EJS:         ["ejs"],
          Elixir:      ["ex|exs"],
          Elm:         ["elm"],
          Erlang:      ["erl|hrl"],
          Forth:       ["frt|fs|ldr|fth|4th"],
          Fortran:     ["f|f90"],
          FTL:         ["ftl"],
          Gcode:       ["gcode"],
          Gherkin:     ["feature"],
          Gitignore:   ["^.gitignore"],
          Glsl:        ["glsl|frag|vert"],
          Gobstones:   ["gbs"],
          golang:      ["go"],
          GraphQLSchema: ["gql"],
          Groovy:      ["groovy"],
          HAML:        ["haml"],
          Handlebars:  ["hbs|handlebars|tpl|mustache"],
          Haskell:     ["hs"],
          Haskell_Cabal:     ["cabal"],
          haXe:        ["hx"],
          Hjson:       ["hjson"],
          HTML:        ["html|htm|xhtml"],
          HTML_Elixir: ["eex|html.eex"],
          HTML_Ruby:   ["erb|rhtml|html.erb"],
          INI:         ["ini|conf|cfg|prefs"],
          Io:          ["io"],
          Jack:        ["jack"],
          Jade:        ["jade|pug"],
          Java:        ["java"],
          JavaScript:  ["js|jsm|jsx"],
          JSON:        ["json"],
          JSONiq:      ["jq"],
          JSP:         ["jsp"],
          JSX:         ["jsx"],
          Julia:       ["jl"],
          Kotlin:      ["kt|kts"],
          LaTeX:       ["tex|latex|ltx|bib"],
          LESS:        ["less"],
          Liquid:      ["liquid"],
          Lisp:        ["lisp"],
          LiveScript:  ["ls"],
          LogiQL:      ["logic|lql"],
          LSL:         ["lsl"],
          Lua:         ["lua"],
          LuaPage:     ["lp"],
          Lucene:      ["lucene"],
          Makefile:    ["^Makefile|^GNUmakefile|^makefile|^OCamlMakefile|make"],
          Markdown:    ["md|markdown"],
          Mask:        ["mask"],
          MATLAB:      ["matlab"],
          Maze:        ["mz"],
          MEL:         ["mel"],
          MUSHCode:    ["mc|mush"],
          MySQL:       ["mysql"],
          Nix:         ["nix"],
          NSIS:        ["nsi|nsh"],
          ObjectiveC:  ["m|mm"],
          OCaml:       ["ml|mli"],
          Pascal:      ["pas|p"],
          Perl:        ["pl|pm"],
          pgSQL:       ["pgsql"],
          PHP:         ["php|phtml|shtml|php3|php4|php5|phps|phpt|aw|ctp|module"],
          Pig:         ["pig"],
          Powershell:  ["ps1"],
          Praat:       ["praat|praatscript|psc|proc"],
          Prolog:      ["plg|prolog"],
          Properties:  ["properties"],
          Protobuf:    ["proto"],
          Python:      ["py"],
          R:           ["r"],
          Razor:       ["cshtml|asp"],
          RDoc:        ["Rd"],
          RHTML:       ["Rhtml"],
          RST:         ["rst"],
          Ruby:        ["rb|ru|gemspec|rake|^Guardfile|^Rakefile|^Gemfile"],
          Rust:        ["rs"],
          SASS:        ["sass"],
          SCAD:        ["scad"],
          Scala:       ["scala"],
          Scheme:      ["scm|sm|rkt|oak|scheme"],
          SCSS:        ["scss"],
          SH:          ["sh|bash|^.bashrc"],
          SJS:         ["sjs"],
          Smarty:      ["smarty|tpl"],
          snippets:    ["snippets"],
          Soy_Template:["soy"],
          Space:       ["space"],
          SQL:         ["sql"],
          SQLServer:   ["sqlserver"],
          Stylus:      ["styl|stylus"],
          SVG:         ["svg"],
          Swift:       ["swift"],
          Tcl:         ["tcl"],
          Tex:         ["tex"],
          Text:        ["txt"],
          Textile:     ["textile"],
          Toml:        ["toml"],
          TSX:         ["tsx"],
          Twig:        ["twig|swig"],
          Typescript:  ["ts|typescript|str"],
          Vala:        ["vala"],
          VBScript:    ["vbs|vb"],
          Velocity:    ["vm"],
          Verilog:     ["v|vh|sv|svh"],
          VHDL:        ["vhd|vhdl"],
          Wollok:      ["wlk|wpgm|wtest"],
          XML:         ["xml|rdf|rss|wsdl|xslt|atom|mathml|mml|xul|xbl|xaml"],
          XQuery:      ["xq"],
          YAML:        ["yaml|yml"],
          Django:      ["html"]
        }
        ;

    //ace.config.set('basePath', '/js/jam/ace/1.2.8/src-noconflict');
    //ace.config.set('modePath', '/js/jam/ace/1.2.8/src-noconflict');
    //ace.config.set('themePath', '/js/jam/ace/1.2.8/src-noconflict');
/*
    ace.config.set('basePath', '/md2pdf/inc/ace');
    ace.config.set('modePath', '/md2pdf/inc/ace');
    ace.config.set('themePath', '/md2pdf/inc/ace');
*/
    function Instance(pluggable,element,dd){
      var that = this;
      this.pluggable = pluggable;
      this.element = element;
      this.o = element;
      this.t = 'ace';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') { return; };

        if (typeof(rec['value'])!='undefined') {
          var val = rec['value'];
          that.editor.setValue(val, 1);
        };
        if (typeof(rec['append'])!='undefined') {
          var val = rec['append'];
          var lineNumber = that.editor.session.getLength();
          that.editor.gotoLine(lineNumber);
          that.editor.insert(val);
        };
        if (typeof(rec['insert'])!='undefined') {
          var val = rec['insert'];
          that.editor.insert(val);
        };
        if (typeof(rec['replace'])!='undefined') {
          var val = rec['replace'];
          that.editor.session.replace(that.editor.getSelectionRange(), val);
        };
        if (typeof(rec['theme'])!='undefined') {
          var val = rec['theme'];
          that.editor.setTheme(val);
        };
        if (typeof(rec['mode'])=='string') {
          var val = rec['mode'];
          that.editor.getSession().setMode(val);
        };
        if ((typeof(rec['send'])=='object')
        &&(typeof(rec['send'].url)=='string')) {
          var oo = rec['send'];var key = 'value';
          if (typeof(rec['send'].key)=='string'){
            key = rec['send'].key;
          };
          oo[key] = that.editor.getValue();
          that.pluggable.sendData(oo, oo.url, that);
        };
        if ((typeof(rec['send/selection'])=='object')
        &&(typeof(rec['send/selection'].url)=='string')) {
          var oo = rec['send/selection'];var key = 'value';
          if (typeof(rec['send/selection'].key)=='string'){
            key = rec['send'].key;
          };
          oo[key] = that.editor.getSelectedText();
          that.pluggable.sendData(oo, oo.url, that);
        };        
        
      },
      
      this.getRecord = function() {
        return $(that.element).text();
      },

      this.getSelection = function() {
        return that.editor.session.getTextRange(that.editor.getSelectionRange());
      },

      this.search = function() {
        editor.find('needle',{
            backwards: false,
            wrap: false,
            caseSensitive: false,
            wholeWord: false,
            regExp: false
        });
        editor.findNext();
        editor.findPrevious();
      },

      this._go_open = function(e) {
        console.log('open');
        $(that.element).trigger('waxed-request-open', $(e.target));

      },

      this._go_close = function(e) {
        console.log('close');
        $(that.element).trigger('waxed-request-close', $(e.target));

      },

      this._go_save = function(e) {
        console.log('save');
        $(that.element).trigger('waxed-request-save', $(e.target));

      },

      this._go_search = function(e) {
        console.log('search');
        $(that.element).trigger('waxed-request-search', $(e.target));

      },

      this.free = function() {
        that.editor.destroy();
        $(that.div).remove();
      },

      this.init=function() {
        console.log('ACE!');

        //var path = that.pluggable._routes.plugin;
        var path="/assets/";
        ace.config.set('basePath', path+'ace');
        ace.config.set('modePath', path+'ace');
        ace.config.set('themePath', path+'ace');

        that.form = $(that.element).closest('form');
        var source = $(that.element).text();
        that.div = $('<div></div>').insertAfter(that.element);
        var h = $(that.element).height();
        var w = $(that.element).width();
        $(that.div).css({height:Math.round(h)+'px'});
        $(that.element).css({visibility:'hidden', height:'0px',display:'none'});
        var id = that.pluggable.getDomId(that.div);
        that.editor = ace.edit(id);
        console.log(that.editor);
        /*
        that.editor.setTheme("ace/theme/monokai");
        that.editor.session.setOptions({
            mode: "ace/mode/javascript",
            tabSize: 2,
            useSoftTabs: true
        });
        */
        var el = that.editor.textInput.getElement();
        console.log(el);
        $(el).on('keydown', function(e){
          console.log('keydown', e);
          if (!e.originalEvent.altKey) {
            if (e.originalEvent.ctrlKey) {
              switch (e.originalEvent.key) {
                case 's': that._go_save(e); e.preventDefault(); break;
                case 'o': that._go_open(e); e.preventDefault(); break;
                case 'q': that._go_close(e); e.preventDefault(); break;

              };
              return true;
            }
            if (e.originalEvent.shiftKey) {
              switch (e.originalEvent.keyCode) {
                case 161: that.editor.insert("!"); e.preventDefault(); break;
                //case 192: that.editor.insert(";"); e.preventDefault(); break;
              };
              return true;

            };

            switch (e.originalEvent.keyCode) {
              case 161: that.editor.insert("'"); e.preventDefault(); break;
              case 192: that.editor.insert(";"); e.preventDefault(); break;

            };
            return true;
          }
          switch (e.originalEvent.keyCode) {
            case 59: that.editor.insert("`"); e.preventDefault(); break;
            case 192: that.editor.insert("`"); e.preventDefault(); break;//~
            case 49: that.editor.insert("!"); e.preventDefault(); break;
            case 50: that.editor.insert("@"); e.preventDefault(); break;
            case 51: that.editor.insert("#"); e.preventDefault(); break;
            case 52: that.editor.insert("$"); e.preventDefault(); break;
            case 53: that.editor.insert("%"); e.preventDefault(); break;
            case 54: that.editor.insert("^"); e.preventDefault(); break;
            case 55: that.editor.insert("&"); e.preventDefault(); break;
            case 56: that.editor.insert("*"); e.preventDefault(); break;
            case 57: that.editor.insert("("); e.preventDefault(); break;
            case 58: that.editor.insert(")"); e.preventDefault(); break;
            case 59: that.editor.insert("-"); e.preventDefault(); break;
            case 60: that.editor.insert("+"); e.preventDefault(); break;

            case 66: that.editor.insert("{"); e.preventDefault(); break;
            case 78: that.editor.insert("}"); e.preventDefault(); break;

            case 161: that.editor.insert("'"); e.preventDefault(); break;
            case 162: that.editor.insert(";"); e.preventDefault(); break;

            case 65: that.editor.insert("~"); e.preventDefault(); break;



          };



        });
        //$(el).on('keyup', function(e){console.log('keyup', e)});
        //console.log(id, that.div, that.editor);
        that.editor.getSession().setUseWorker(false);

        if(typeof(this.dd.theme)!='undefined'){
          that.editor.setTheme(this.dd.theme);
        } else {
          that.editor.setTheme("ace/theme/monokai");
        };
        if(typeof(this.dd.mode)!='undefined'){
          that.editor.getSession().setMode(this.dd.mode);
        } else {
          that.editor.getSession().setMode("ace/mode/javascript");
        };
        that.editor.setOption("tabSize", 2);
        that.editor.setOption("useSoftTabs", true);
        
        that.editor.setValue(source, 1);
        that.editor.on("change", function(e){
           //console.log(e);
           var source = that.editor.getValue();
           $(that.element).text(source);
           $(that.element).trigger('jam-form-changed', $(e.target));
        });
        //setTimeout(function(){
          //that.editor.resize();
          //that.editor.renderer.updateFull();
        //}, 500);
        that.editor.commands.addCommand({
            name: 'myCommand1',
            bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
            exec: function(editor) {
              //console.log(editor);
              $(that.form).submit();
            },
            readOnly: false // false if this command should not apply in readOnly mode
        });
        inited = true;
      },
      this._init_();
    }



    $.waxxx(pluginName, _search, Instance, _api);

})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/bs_modal_multi/plugin.js
