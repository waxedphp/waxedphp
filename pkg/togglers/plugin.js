
;(function ( $, window, document, undefined ) {

    var pluginName = 'togglers',
        _search = '.waxed-togglers',
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
      this.radios = [];
      this.value = null;
      this.cfg = {
      };

      this.invalidate = function(RECORD){
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') { return; };
        //console.log($(that.element).closest('div'));
        $(that.element).parent().closest('div').find('.waxed-plugin-invalidation').each(function(i, elem){
          $(elem).empty();
          for(var i=0; i<rec.length; i++){
            $('<p>').appendTo(elem).text(rec[i]);
          }
        });
        //console.log('INVALIDATE', rec);


      },

      this.setRecord = function(RECORD){
        if (typeof that.dd.name == 'undefined') return;
        var rec = that.pluggable.getvar(that.dd.name, RECORD);
        if (typeof rec != 'object') {
          this.select(rec);
          return;
        };
        if (typeof rec.items == 'object') {
          this.setItems(rec.items);
        };
        if (typeof rec.disable == 'object') {
          this.disable(rec.disable);
        };

        if ((typeof rec.value == 'string')||(typeof rec.value == 'number')) {
          this.select(String(rec.value));
        };

      },


      this.free = function() {

      },

      this.setItems = function(items) {
        $(this.element).empty();
        this.radios = [];
        for(var i=0; i<items.length; i++){

          var input = $('<input type="radio" >').appendTo(that.element);
          var id = this.pluggable.getDomId(input);
          var label = $('<label for="'+id+'" >').appendTo(that.element);
          label.text(items[i].text);
          input.val(items[i].value);
          input.prop('name', this.dd.name);
          this.radios.push(input);
          $(input).click(function(){
            that.changed(this);
          });
        }
      },

      this.disable = function(val) {
        console.log('DISABLE', val);
        for(var i=0; i<this.radios.length; i++){
          if (val.includes($(this.radios[i]).val())) {
            $(this.radios[i]).prop('disabled', true);
          } else {
            $(this.radios[i]).prop('disabled', false);
          }
        }
      },

      this.select = function(val) {
        for(var i=0; i<this.radios.length; i++){
          if ($(this.radios[i]).val() == val) {
            $(this.radios[i]).prop('checked', true);
            return;
          };
        }
      },

      this.changed = function(elem) {
        if($(elem).is(':checked')) {
          this.value = $(elem).val();
        };
        //console.log(this.value);
        if (
          (typeof this.dd.url !== 'undefined')&&
          (typeof this.dd.action !== 'undefined')&&
          (typeof this.dd.name !== 'undefined')
        ) {
          $(that.element).parent().closest('div').find('.waxed-plugin-invalidation').each(function(i, elem){
            $(elem).empty();
          });
          var data = {};
          data.action = this.dd.action;
          data[that.dd.name] = this.value;
          if (typeof this.dd.csrf === 'string') {
            data._token = this.dd.csrf;
          };
          this.pluggable.sendData(data, this.dd.url);
        }
      },

      this.init=function() {
        $(that.element).find('input[type=radio]').each(function(i,elem){
          //console.log(i,elem);
          that.radios.push(elem);
          if($(elem).is(':checked')) {
            that.value = $(elem).val();
          };
          $(elem).click(function(){
            that.changed(this);
          });
          $(elem).addClass('waxed-plugin-input');
        });
        console.log(this.value);
        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/toggleradios/plugin.js
