
;(function ( $, window, document, undefined ) {

    var pluginName = 'moment',
        _search = '.waxed-moment',
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
      this.t = 'moment';
      this.dd = dd;
      this.name = '';
      this.cfg = {
        fmt: "YYYY-MM-DD HH:mm:ss",
        tz: false
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') { return; };
        if (typeof rec.timezone == 'string') {
          if (moment.tz.zone(rec.timezone)!==null) {
            that.cfg.tz = rec.timezone;
          }
        };
        if (typeof rec.format == 'string') {
          that.cfg.fmt = rec.format;
        };
        if ((typeof rec.value == 'string') || (typeof rec.value == 'number')) {
          this._display(rec.value);
        };
      },

      this._display = function(timestamp) {

        switch (typeof timestamp) {
          case 'string':
            var m = moment(timestamp);
          break;
          case 'number':
            var m = moment(timestamp);
          break;
          default:
            var m = moment();
          break;
        };
        if (typeof that.cfg.tz == 'string') {
          if (moment.tz.zone(that.cfg.tz)!==null) {
            m = m.tz(that.cfg.tz);
          };
        }
        var s = m.format(that.cfg.fmt);
        s = s.replace('{timezone}', that.cfg.tz);
        $(that.element).html(s);
        return s;
      },


      this.free = function() {

      },

      this.init=function() {

        if (typeof that.dd.timezone == 'string') {
          if (moment.tz.zone(that.dd.timezone)!==null) {
            that.cfg.tz = that.dd.timezone;
          }
        }
        if (typeof that.dd.format == 'string') that.cfg.fmt = that.dd.format;

        //var fmt = "YYYY-MM-DD HH:mm:ss";
        //var tz = "America/Toronto";

        if (typeof that.dd.value == 'undefined') {
          that.dd.value = null;
          var s = $(that.element).text();
          s = s.trim();
          console.log(s);
          if (s != '') that.dd.value = s;
          try {
            var n = parseInt(s);
            if (n>0) that.dd.value = n;
            console.log('NUMBER',n);
          }
          catch(err) {
            console.log(err);
          }
        };

        that.dd.value = this._display(that.dd.value);

        //console.log(moment.tz.names());




        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
