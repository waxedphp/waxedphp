
;(function ( $, window, document, undefined ) {

    var pluginName = 'fontawesome',
        _search = '^body',
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
      this.t = 'fontawesome';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){

      },


      this.free = function() {

      },

      this._addCss=function(rule) {
        let css = document.createElement('style');
        css.type = 'text/css';
        if (css.styleSheet) css.styleSheet.cssText = rule; // Support for IE
        else css.appendChild(document.createTextNode(rule)); // Support for the rest
        document.getElementsByTagName("head")[0].appendChild(css);
      },

      this.init=function() {
        return;
        console.log('FONTAWESOME');
        if (inited) return;
        var s = '';
        var p = that.pluggable._routes.plugin+'fontawesome/';

        s += '@font-face {font-family: \'FontAwesome\';';
        s += 'src: url(\'' + p + 'fontawesome-webfont.eot?v=4.7.0\');';
        s += 'src: url(\'' + p + 'fontawesome-webfont.eot?#iefix&v=4.7.0\') format(\'embedded-opentype\'), ';
        s += 'url(\'' + p + 'fontawesome-webfont.woff2?v=4.7.0\') format(\'woff2\'), ';
        s += 'url(\'' + p + 'fontawesome-webfont.woff?v=4.7.0\') format(\'woff\'), ';
        s += 'url(\'' + p + 'fontawesome-webfont.ttf?v=4.7.0\') format(\'truetype\'), ';
        s += 'url(\'' + p + 'fontawesome-webfont.svg?v=4.7.0#fontawesomeregular\') format(\'svg\');';
        s += 'font-weight: normal;font-style: normal;}';

        this._addCss(s);



        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
