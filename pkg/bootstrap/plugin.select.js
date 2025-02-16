
;(function ( $, window, document, undefined ) {

    var pluginName = 'bootstrapSelect',
        _search = '.waxed-bootstrap-select',
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

      },

      this.free = function() {

      },

      this.init=function() {
        if (typeof that.dd.value != 'undefined') {
          $(that.element).find('option').each(function(i,a){
            var val = $(a).attr('value');
            if (val == that.dd.value) {
              $(a).attr('selected', 'selected');
            } else {
              $(a).removeAttr('selected');
            }
            //console.log(i,a,$(a).attr('value'));
          });
          
        };


        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
