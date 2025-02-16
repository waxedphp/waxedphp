
;(function ( $, window, document, undefined ) {

    var pluginName = 'bootstrap',
        _search = '.waxed-bootstrap-button',
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
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') { return; };

      },


      this.free = function() {

      },

      this.init=function() {

        $(that.element).find('input[type=submit]').each(function(i,a){
          $(a).on('click', function(e){
            $(e.currentTarget).removeClass('success');
            $(e.currentTarget).removeClass('error');
            $(e.currentTarget).addClass('animate');
            setTimeout(function(){
              $(e.currentTarget).removeClass('animate');
              $(e.currentTarget).addClass('success');
            }, 2000);
          });
        });

        $(that.element).find('button[type=submit]').each(function(i,a){
          $(a).on('click', function(e){
            $(e.currentTarget).removeClass('success');
            $(e.currentTarget).removeClass('error');
            $(e.currentTarget).addClass('animate');
            setTimeout(function(){
              $(e.currentTarget).removeClass('animate');
              if (that.dd.result=='error') {
                $(e.currentTarget).addClass('error');
              } else if (that.dd.result=='success') {
                $(e.currentTarget).addClass('success');
              }
            }, 2000);
          });
        });

        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
