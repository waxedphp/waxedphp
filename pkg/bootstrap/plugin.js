
;(function ( $, window, document, undefined ) {

    var pluginName = 'bootstrap',
        _search = '^body.waxed-bootstrap',
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
      this.t = pluginName;
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        if (typeof that.dd.bsName == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.bsName, RECORD);
        if (typeof rec != 'object') { return; };
        //console.log('BOOTSTRAPREPORT', rec);
        if (typeof rec.reportSize == 'string') that.reportSize(rec.reportSize);
      },

      this.reportSize = function(url) {
        that.pluggable.sendData({
          'action':'viewport',
          'width':window.innerWidth,
          'height':window.innerHeight
        }, url, that);
      },

      this.free = function() {

      },

      this.init=function() {
        /**
         * Toggle .header-scrolled class to #header when page is scrolled
         */
        let selectHeader = $('.waxed-header');
        if (selectHeader) {
          const headerScrolled = () => {
            if (window.scrollY > 20) {
              selectHeader.addClass('header-scrolled');
            } else {
              selectHeader.removeClass('header-scrolled');
            }
          }
          headerScrolled();
          window.addEventListener("scroll", headerScrolled);
        }

        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
