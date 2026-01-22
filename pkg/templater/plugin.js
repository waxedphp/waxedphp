
;(function ( $, window, document, undefined ) {

    var pluginName = 'templater',
        _search = '.waxed-templater',
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
      this.t = 'templater';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec == 'string') {
          $(that.element).html(rec);
        };
        if (typeof rec != 'object') { return; };

        var oo = {
          'action': 'display'
        };
        //console.log('ROUTES>>>', that.pluggable._routes);
        oo.element = that.element;
        if (typeof that.dd.template != 'undefined') {
          oo.template = that.pluggable._routes.design + '' + that.dd.template;
          oo.RECORD = rec;
          that.pluggable.trigger(oo);
        } else if(typeof rec.template == 'string') {
          oo.template = rec.template;
          oo.RECORD = rec;
          that.pluggable.trigger(oo);
        }

      },
      
      this.show = function(template, rec) {
        var oo = {
          'action': 'display'
        };
        //console.log('ROUTES>>>', that.pluggable._routes);
        oo.element = that.element;        
        oo.template = that.pluggable._routes.design + '' + template;
        oo.RECORD = rec;
        that.pluggable.trigger(oo);        
      },


      this.free = function() {

      },

      this.init=function() {
        
        if (typeof that.dd.template != 'undefined') {
          //that.show(that.dd.template, {});
        }

        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
