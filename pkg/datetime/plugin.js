/*
 *
 *
 * updated 20190611
 * updated 20231015
 *
 * npm i flatpickr --save


 *
 */
;(function ( $, window, document, undefined ) {

    var pluginName = 'waxed/datetime',
        _search = '.waxed-datetime',
        defaults = {
            propertyName: "value"
        },
        inited = false,
        _api = []
        ;


    function Instance(pluggable,element,dd){
      var that = this;
      this.pluggable = pluggable;
      this.element = element;
      this.o = element;
      this.t = 'flatpickr';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){
        //console.log('SETTING', RECORD);
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') { return; };
        console.log('SETTING DATETIME', rec, typeof rec['value']);
        if (typeof rec['value'] == 'string') {
          var d = moment(rec['value']);
          that.picker.setDate(d.toDate());
        };
        if ((typeof rec['from'] == 'string')&&(typeof rec['to'] == 'string')) {
          var d1 = moment(rec['from']);
          var d2 = moment(rec['to']);
          //that.picker.setDateRange(d1.toDate(),d2.toDate());
        };
        if (typeof rec['open'] == 'boolean') {
          if (rec.open) {
            that.picker.open();
          } else {
            that.picker.close();
          };
        };

        if (typeof rec['enableDates'] == 'object') {
          that.picker.set('enable', rec['enableDates']);
        };
        if (typeof rec['disableDates'] == 'object') {
          that.picker.set('disable', rec['disableDates']);
        };

      },


      this.free = function() {
        that.picker.destroy();

      },

      this.init=function() {
        console.log('FLATPICKR');
        this.cfg = {
          enableTime:true,
          //plugins: [new confirmDatePlugin({})],
          time_24hr:true,
          dateFormat: 'Y-m-d H:i',
          altFormat: 'Y-m-d H:i'
        };

        if ($(that.element).hasClass('inline')) {
          this.cfg.inline = true;
          this.cfg.allowInput = false;
        };

        this.cfg.minuteIncrement = 1;

        if ($(that.element).hasClass('nocalendar')) {
          this.cfg.noCalendar = true;
          this.cfg.dateFormat = 'H:i';
        };

        if ($(that.element).hasClass('notime')) {
          this.cfg.enableTime = false;
          this.cfg.dateFormat = 'Y-m-d';
        };

        if ($(that.element).hasClass('splitview')) {
        //  this.cfg.splitView = true;
        };

        if ((typeof that.dd.datetimemode == 'string')
          &&($.inArray(that.dd.datetimemode, ['single','multiple','range'])>-1)) {
          this.cfg.mode = that.dd.datetimemode;
        };
        console.log('flatpickr',this.cfg);
        that.picker = $(that.element).flatpickr(this.cfg);

        if ($(that.element).hasClass('hidden')) {
          $(that.element).css({'visibility':'hidden','height':'1px'});
        }

        if ($(that.element).hasClass('opened')) {
        //  that.picker.show();
        };



        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);

})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
