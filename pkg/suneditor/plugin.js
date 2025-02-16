;(function ( $, window, document, undefined ) {

    var pluginName = 'suneditor',
        _search = 'textarea.waxed-suneditor',
        _api = [],
        defaults = {
            propertyName: "value"
        },
        inited = false
        ;

    function Instance(pluggable,element,dd){
      var that = this;
      this.pluggable = pluggable;
      this.element = element;
      this.o = element;
      this.t = 'suneditor';
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


        var html = that.editor.getContents();
        if (typeof rec.config == 'object') {

          var cfg = rec.config;
          //cfg.codeMirror = CodeMirror;
          cfg.plugins = SUNEDITORPLUGINS;

          that.editor.destroy();
          that.editor = SUNEDITOR.create(that.element, cfg);
          that.editor.setContents(html);
          that.editor.onSave = that.save;

          that.editor.onBlur = function (e, core) { 
            that.editor.save();
            console.log('SAVE ', e);
          }

        };

        if (typeof rec.value == 'string') {
          that.editor.setContents(rec.value);
        } else {

        };
      },


      this.save = function(content, core) {
        if (typeof that.dd.action == 'undefined') return;
        if (typeof that.dd.name == 'undefined') return;
        var data = {
          "action": that.dd.action
        };
        data[that.dd.name] = content;
        that.pluggable.sendData(data, that.pluggable.ajaxUrl, this);
      },


      this.free = function() {
        //$(that.element).summernote('destroy');

      },

      this.init=function() {

        var cfg = {
          width:'100%'
        };

        that.editor = SUNEDITOR.create(that.element, cfg);
    // All of the plugins are loaded in the "window.SUNEDITOR" object in dist/suneditor.min.js file
    // Insert options
    // Language global object (default: en)
    //lang: SUNEDITOR_LANG['ko']
    
        that.editor.onBlur = function (e, core) { 
          that.editor.save();
          console.log('SAVE ', e);
        }




        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
