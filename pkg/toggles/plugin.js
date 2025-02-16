
;(function ( $, window, document, undefined ) {

    var pluginName = 'toggles',
        _search = '.waxed-toggles',
        _api = [],
        defaults = {
            propertyName: "value"
        };

    function Instance(pluggable,element,dd){
      var that = this;
      this.pluggable = pluggable;
      this.element = element;
      this.o = element;
      this.t = 'toggles';
      this.dd = dd;
      this.name = '';
      this._bitwise = 0;
      this.cfg = {
      text:{
      'on': '',
      'off': ''
      },
      type:'compact'
      };


      this.invalidate = function(RECORD){
        $(this.element).removeClass('invalid');
        this.setRecord(RECORD);
        if(typeof(this.dd.name)=='undefined')return false;
        if(this.dd.name in RECORD){
          $(this.element).addClass('invalid');
          $(this.element).before('<label class="invalid">'+RECORD[this.dd.name]+'</label>');
        };
      },

      this.setRecord = function(RECORD){
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec == 'undefined') {
          return;
        };
        //var myToggle = $(this.element).data('toggles');
        var val = this._getVal(rec);
        this.toggle.toggle(val, false, true);
        this._setVal(val);
      },

      this._getVal = function(val) {
          if (this._bitwise > 0) {
            return (val&this._bitwise) > 0;
          } else {
            return ((val===true) || ( val in {'1':1,'on':1,'true':1}));
          }
      },

      this._setVal = function(val) {
        if ($(this.input).is("input")){
          if (this._bitwise > 0) {
            var n = $(this.input).val();
            if (val) {
              n = n|this._bitwise;
            } else {
              n = n&(~this._bitwise);
            }
            $(this.input).val(n);
          } else {
            $(this.input).val(val);
          }
        } else if ($(this.input).is("a")){

          if (this._bitwise > 0) {
            var n = parseInt($(this.input).data('value'));
            if (val) {
              n = n|this._bitwise;
            } else {
              n = n&(~this._bitwise);
            }
            $(this.input).data('value', n);
          } else {
            $(this.input).data('value', val);
          }
        }
      },

      this.init=function(){
        //console.log('TOGGLES');

        if (typeof(this.dd.status)!='undefined') {

        };

        //this.cfg.easing = 'linear';
        //this.cfg.animate = 500;
        var id = $(this.element).attr('id');

        if (typeof(this.dd.clicker)=='string') {
          this.cfg.clicker = $(this.dd.clicker);// label to click
        } else {
          if ((typeof id == 'string')&&(id)) {
            var labels = $('label[for="' + id + '"]');
            if (labels.length > 0) {
              this.cfg.clicker = $(labels[0]);
            };
          };
        }
        if (typeof(this.dd.text_on)=='string') {
          this.cfg.text.on = this.dd.text_on;
        };
        if (typeof(this.dd.text_off)=='string') {
          this.cfg.text.off = this.dd.text_off;
        };
        if ((typeof(this.dd.input)=='string')&&($(this.dd.input).length > 0)) {
          this.input = $(this.dd.input);
          if ((typeof(this.dd.bitwise)=='string')||(typeof(this.dd.bitwise)=='number')) {
            this._bitwise = parseInt(this.dd.bitwise);
          };
        } else {
          this.input = $('<input type="hidden" name="' + that.dd.name + '" value="" />').insertAfter(this.element);
        };

        $(this.element).toggles(this.cfg);
        this.toggle = $(this.element).data('toggles');

        if ($(this.input).is("input")){
          var val = $(this.input).val();
          //console.log('INPUT', val);
          if (typeof val != 'undefined')  {
            val = this._getVal(parseInt(val));
            this.toggle.toggle(val, false);
            //this._setVal(val);
          };

        } else if ($(this.input).is("a")){
          var val = $(this.input).data('value');
          //console.log('A', val);
          if (typeof val != 'undefined') {
            val = this._getVal(parseInt(val));
            this.toggle.toggle(val, false);
            //this._setVal(val);
          };
        };

        if (
          (typeof(this.dd.action)!='undefined')
          &&
          (typeof(this.dd.url)!='undefined')
          &&
          (typeof(this.dd.name)!='undefined')
        ) {

          $(this.element).on('toggle', function (e, active) {

            var data = {};
            data.action = that.dd.action;
            data[that.dd.name] = active;
            that.pluggable.sendData(data, that.dd.url);
          });
        };
        if (
          (typeof(this.dd.submit)!='undefined')
          &&(this.dd.submit)
        ) {
          that.form = $(that.element).closest('form');
          $(this.element).on('toggle', function (e, active) {
            that._setVal(active);
            $(that.form).trigger('waxed-form-submit');
          });
        }
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);

})( jQuery, window, document );
