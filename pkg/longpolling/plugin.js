/**
 *
 *
 *
 *
 */
;(function ( $, window, document, undefined ) {

    var pluginName = 'jam/longpolling',
        defaults = {
            propertyName: "value"
        },
        inited = false,
        _api = ['start', 'stop', 'resume', 'restart']
        ;


    function Instance(pluggable,element,dd){
      var that = this;
      this.pluggable = pluggable;
      this.element = element;
      this.o = element;
      this.t = 'longpolling';
      this.dd = dd;
      this.name = '';
      this.cfg = {
        maxTurns:false,
        initiallyStopped:true
      };

      this.invalidate = function(RECORD){

      },

      this.setRecord = function(RECORD){

      },

      this.start = function() {
        this.fifolist.clear();
        this._ans.start();
      },

      this.stop = function() {
        this._ans.stop();
      },

      this.resume = function() {
        this._ans.resume();
      },


      this.restart = function() {
        this._ans.stop();
        this.fifolist.clear();
        this._ans.start();
      },


      this.free = function() {
        this._ans.destroy();
        delete this._ans;
      },

      this.init=function() {
        if (typeof this.dd.maxListLength != 'undefined') {
          this.cfg.maxListLength = this.dd.maxListLength;
        };
        if (typeof this.dd.maxTurns != 'undefined') {
          this.cfg.maxTurns = this.dd.maxTurns;
        };
        if (typeof this.dd.initiallyStopped != 'undefined') {
          this.cfg.initiallyStopped = !(!this.dd.initiallyStopped);
        };
        /*
        $(this.element).on('fifolist-empty fifolist-overflow fifolist-user fifolist-auto', function(ev, a){
          console.log(ev.type, a, ev);
        });
        */
        var fifolistOptions = {
          maxListLength: this.cfg.maxListLength,
          prepend: false,
          api: true
        };
        //this.fifolist = $(that.element).FiFoList(fifolistOptions);
        this.fifolist = $.FiFoList(that.element, fifolistOptions);
        /*
        $('body').on('longpolling-request longpolling-chunk longpolling-progress longpolling-complete longpolling-abort longpolling-error longpolling-success longpolling-all-done', function(ev, a){
          console.log(ev.type, ev.originalEvent.detail);
        });
        */



        this._ans = new LongPolling(
          that.dd.url,
          {
          // These are the default values:
          tag: 'chunk', // tag in php response.
          //listLength: 10, // after that length the listing lines start disapear - first in first out.
          interval: 1000, // interval to parse response without jquery.
          useJQuery: false, // beter is without jQuery, as jQuery cause waiting favicon.
          //prepend: false, // new line should be rather appended or prepended.
          useJSON: false, // server sends JSON chunks wrapped to xml tag.
          //onMessage: 'draw', // string [draw, log, exec] or function custom function.
          maxTurns: this.cfg.maxTurns,
          onChunk: function(message) {
            //$(that.element).FiFoList({line:message});
            that.fifolist.add(message);
          },
          stopped: this.cfg.initiallyStopped // initially stopped. Could be started with method "resume".
        });



        inited = true;
      },
      this._init_();
    }

    if (typeof(document.jammin) == 'undefined') {
      document.jammin = {};
    };
    document.jammin[pluginName] = {
      search:'.jam-longpolling',
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

})( jQuery, window, document );
/*--*/
//# sourceURL: /js/jam/ajax_stream/plugin.js
