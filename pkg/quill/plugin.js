
;(function ( $, window, document, undefined ) {


    var pluginName = 'quill',
        _search = 'div.waxed-quill',
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
      this.t = 'quill';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this._quillGetHTML = function(inputDelta) {
        var tempCont = document.createElement("div");
        (new Quill(tempCont)).setContents(inputDelta);
        return tempCont.getElementsByClassName("ql-editor")[0].innerHTML;
      },

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') { return; };


        var html = that.editor.root.innerHTML;
        if (typeof rec.config == 'object') {

          //var cfg = rec.config;

          //that.editor.destroy();
          //that.editor = new Quill(that.element, cfg);
          //that.editor.setContents(html);

        };

        if (typeof rec.value == 'string') {
          // ti musi jebat...
          that.editor.clipboard.dangerouslyPasteHTML(rec.value);
        } else {

        };
        
      },


      this.free = function() {
        //$(that.element).summernote('destroy');

      },

      this.init=function() {
        console.log('QUILL');

        var cfg = {
          theme: 'snow',
          width: '100%',
          modules: {
            toolbar: [
              [{ header: [1, 2, false] }],
              ['bold', 'italic', 'underline'],
              ['image', 'code-block']
            ]
          },
          placeholder: 'Compose an epic...'

        };

        that.editor = new Quill(that.element, cfg);
        inited = true;

      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
