
;(function ( $, window, document, undefined ) {

    var pluginName = 'bootstrap-pagination',
        _search = '.waxed-bootstrap-pagination',
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
      
      this.click = function(ev,el) {
        //console.log(ev,$(el).data('pg'));
        if (typeof that.dd.action == 'undefined') return;
        
        var pg = 0;
        switch($(el).data('pg')) {
          case 'first':
            pg = 0;
          break;
          case 'last':
            pg = this.high;
          break;
          case 'prev':
            pg = Math.max(this.page-1,0);
          break;
          case 'next':
            pg = Math.min(this.page+1,this.high);
          break;
          default:
            pg = parseInt($(el).data('pg'));
          break;      
        }
        if (isNaN(pg)) pg = 0;
        var offset = pg * this.limit;
        
        var o = {
          'page': pg,
          'offset': offset,
          'action': that.dd.action
        };
        var url = '';
        if (typeof that.dd.url != 'undefined') {
          url = that.dd.url;
        } else {
          url = that.pluggable.getAjaxUrl();
        };
        
        if (typeof that.dd.elemid != 'undefined') {
          o.ELEMID = that.dd.elemid;
        };
        
        that.pluggable.sendData(o,url,that);
        
      },
      
      this.build = function() {
        var o = $(that.element);
        o.empty();
        
        var high = Math.floor(this.count / this.limit);
        var pos = Math.floor(this.offset / this.limit);
        pos = Math.min(high, pos);
        this.page = pos;
        this.high = high;
        var min = Math.max(0, pos - 2);
        var max = Math.min(high, pos + 2);
        console.log(high,pos,min,max);
        console.log(this.limit);
        console.log(this.offset);
        
        var a = $('<li class="page-item"></li>').appendTo(o);
        if (pos>0) {
          var b = $('<a class="page-link" href="#" data-pg="first" aria-label="First"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();that.click(ev,this);});
        } else {
          a.addClass('disabled');
          var b = $('<a class="page-link" href="#" aria-label="First"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();});          
        }
        var c = $('<span aria-hidden="true">&larrb;</span>').appendTo(b);
        
        var a = $('<li class="page-item"></li>').appendTo(o);
        if (pos>0) {
          var b = $('<a class="page-link" href="#" data-pg="prev" aria-label="Previous"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();that.click(ev,this);});
        } else {
          a.addClass('disabled');
          var b = $('<a class="page-link" href="#" aria-label="Previous"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();});          
        }
        var c = $('<span aria-hidden="true">&lArr;</span>').appendTo(b);
        for (var i=min;i<=max;i++) {
          var c = $('<li class="page-item"></li>').appendTo(o);
          var d = $('<a class="page-link" href="#" data-pg="'+i+'" >'+(i+1)+'</a>').appendTo(c).on('click', function(ev){ev.preventDefault();that.click(ev, this);});
          if (i==pos) $(c).addClass('active');
        }
        var a = $('<li class="page-item"></li>').appendTo(o);
        if (pos<high) {
          var b = $('<a class="page-link" href="#" data-pg="next" aria-label="Next"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();that.click(ev,this);});
        } else {
          a.addClass('disabled');
          var b = $('<a class="page-link" href="#" aria-label="Next"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();});
        }
        var c = $('<span aria-hidden="true">&rArr;</span>').appendTo(b);

        var a = $('<li class="page-item"></li>').appendTo(o);
        if (pos<high) {
          var b = $('<a class="page-link" href="#" data-pg="last" aria-label="Last"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();that.click(ev,this);});
        } else {
          a.addClass('disabled');
          var b = $('<a class="page-link" href="#" aria-label="Last"></a>').appendTo(a).on('click', function(ev){ev.preventDefault();});
        }
        var c = $('<span aria-hidden="true">&rarrb;</span>').appendTo(b);

      },

      this.free = function() {

      },

      this.init=function() {
        this.count = 0;if (typeof that.dd.count != 'undefined') this.count = parseInt(that.dd.count);
        this.limit = 10;if (typeof that.dd.limit != 'undefined') this.limit = parseInt(that.dd.limit);
        this.offset = 0;if (typeof that.dd.offset != 'undefined') this.offset = parseInt(that.dd.offset);
        if (isNaN(this.count)) this.count = 0;
        if (isNaN(this.limit)) this.limit = 10;
        if (isNaN(this.offset)) this.offset = 0;

        this.build();
        inited = true;
      },
      this._init_();
    }

    $.waxxx(pluginName, _search, Instance, _api);


})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/boilerplate/plugin.js
