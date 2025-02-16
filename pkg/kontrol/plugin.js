
;(function ( $, window, document, undefined ) {

    var pluginName = 'kontrol',
        _search = '.waxed-kontrol',
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
      this.t = 'kontrol';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        //$(that.element).val(27).trigger('change');
      },


      this.free = function() {

      },

      this._set = function(v) {
        that.form = $(that.element).closest('form');
        console.log(v, 'SUBMIT!');
        $(that.form).trigger('waxed-form-submit');
        //console.log(v);
      },

      this.init=function() {
        var cfg = {
          release: function (v) {
            that._set(v);
          }
        };
        
        console.log(typeof(that.dd.max));
        if (typeof(that.dd.min)!='undefined') {
          cfg.min = that.dd.min;
        };
        if (typeof(that.dd.max)!='undefined') {
          cfg.max = that.dd.max;
        };

        if ($(this.element).hasClass('kontrol-knob')) {
          this.mode = 'knob';
          $(this.element).knob(cfg);

        } else if ($(this.element).hasClass('kontrol-dial')) {
          this.mode = 'dial';
          $(this.element).dial(cfg);

        } else if ($(this.element).hasClass('kontrol-xy')) {
          this.mode = 'xy';
          $(this.element).xy(cfg);

        } else if ($(this.element).hasClass('kontrol-bars')) {
          this.mode = 'bars';
          $(this.element).bars(cfg);

        } else {

        }
        inited = true;
      },
      this._init_();
    }

    /*
    if (typeof(document.jammin) == 'undefined') {
      document.jammin = {};
    };
    document.jammin[pluginName] = {
      search:'.jam-kontrol',
      getInstance:function(plug, elem, data) {
        //var data = $(elem).data();
        if(!data['plugin_'+pluginName]){
          $(elem).trigger('jam-plugin-instance-create', pluginName);
          plug.plugExtend(Instance, _api);
          var o = new Instance(plug,elem,data)._api_();
          $.data(elem,'plugin_'+pluginName, o);
          return o;
        }else{
          return data['plugin_'+pluginName];
        }
      }
    };
    */
    $.waxxx(pluginName, _search, Instance, _api);

})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
