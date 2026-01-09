/***********************************************************************
 * WAX framework
 * main JS jquery plugin
 * copyright Andy Bezak 2012 - 2018
 *
 *
 **********************************************************************/
;(function ( $, window, document, undefined ) {

    /**
     * static variables:
     */
    var pluginName = 'waxxx',
        defaults = {
            url: "ajax.php",
            fade_in_time: 1
        },
        hashLastState = '',
        uniqueid = 1,
        plugid = 1,
        _jams = {
          o:{},
          c:{},
          addApi:function(element, api, opt) {
            var id = '#' + this.getDomId(element);
            this.o[id] = {
              'elm': element,
              'api': api
            };
            if ((typeof(opt.pickid)!='undefined')&&(opt.pickid!=id)) {
              this.o[opt.pickid] = this.o[id];
            };
          },
          getApi:function(name) {
            /*
            if (typeof(this.o[name])!='undefined') {
              return this.o[name];
            };
            */
            //console.log('GETAPI', name, this.c);
            if (typeof(this.o[name])!='undefined') {
              var a = this.o[name]['api'];
              var e = this.o[name]['elm'];
              if (e &&($.data(e, 'plugin_' + pluginName))) {
                //console.log('GETAPI-FIND', a);
                return a;
              } else {
                delete this.o[name];
              };
            };
            return undefined;
          },
          getApis:function() {
            return this.o;
          },
          count:function() {
            return this.o.length;
          },
          getUniqueDomId: function() {
            var prefix = pluginName+'_automated_id_';
            var id = prefix + uniqueid;
            uniqueid++;
            return id;
          },
          getAwaitId: function() {
            var prefix = 'await_';
            var id = prefix + uniqueid;
            uniqueid++;
            return id;
          },
          getDomId: function(element) {
            var id = $(element).attr('id');
            if (typeof id == 'undefined') {
              id = this.getUniqueDomId();
              $(element).attr('id', id)
            }
            return id;
          }
        },
        _loaded = {
          js: [],
          css: [],
        };
    var stopwatch = function(name) {
      this.name = name;
      this.start = performance.now();
      this.stop = function(){
        this.end = performance.now();
        //console.log(this.name, ':', this.end - this.start);
      },
      this._w = function(a) {
        this.stop();
        return a;
      }
    };

    /*******************************************************************
     * Templating engine:
     * Interface for templating engine of choice,
     * available:
     * hogan, mustache, smarty, tempo, underscore
     * Default is: mark2
     * Could be accessed only inside this plugin.
     *
     */
    var Template = {
      o:{},
      t:{},
      engine:'mark2',
      setEngine:function(e){
        this.engine=e;
        switch(this.engine){
          case 'underscore':
            _.templateSettings = {
              interpolate: /\{\{(.+?)\}\}/g
            };
          break;
          case 'handlebars':
            Handlebars.registerHelper(pluginName+'', function(items, options) {
              return 'funky';
              var out = "<ul>";

              for(var i=0, l=items.length; i<l; i++) {
                out = out + "<li>" + options.fn(items[i]) + "</li>";
              }

              return out + "</ul>";
            });
            Handlebars.registerDecorator(pluginName+'d', function(program, props, container, context, data, blockParams, depths) {
              //console.log(program, props, container, context, data, blockParams, depths);
            });
          break;
          case 'nunjucks':
            nunjucks.configure('/views', {
                autoescape: true
            });
          break;
        }
      },
      /***
       * private function _w
       * for measure time
       */
      _w:function(template, d){
        d.stop();
        return template;
      },
      /***
       * private function _s
       * for rendering
       */
      _s:function(template, RECORD){
        RECORD.LOCATION=String(document.location);
        var d = new stopwatch('render '+this.engine);
        switch(this.engine){
          case 'none':
          case 'markdown':
            return this.o[template];
            break;
          case 'markup':
            return d._w(Mark.up(this.o[template], RECORD));
            break;
          case 'mark2':
            return d._w(Mark.render(this.o[template], RECORD),d);
            break;
          case 'mark2+':
            return d._w(Mark.render(this.o[template], RECORD),d);
            break;
          case 'nunjucks':
            return d._w(nunjucks.render(this.o[template], RECORD),d);
            //return nunjucks.render(template, RECORD);
            break;
          case 'smarty':
            return d._w(this.o[template].fetch(RECORD),d);
            break;
          case 'mustache':
            return d._w(Mustache.render(this.t[template],RECORD),d);
            break;
          case 'hogan':
            return d._w(this.o[template].render(RECORD),d);
            break;
          case 'handlebars':
            return d._w(this.o[template](RECORD),d);
            break;
          case 'tempo':
            return d._w(this.o[template].render(RECORD),d);
            break;
          case 'underscore':
            return d._w(this.o[template](RECORD),d);
            break;
        };
      },
      /***
       * private function _i
       * for including
       */
      _i:function(template, tplText){
        var d = new stopwatch('compile '+this.engine);
        switch(this.engine){
          case 'none':
          case 'markup':
            this.o[template] = tplText;
            break;
          case 'mark2':
            this.o[template] = d._w(Mark.compile(tplText));
            break;
          case 'mark2+':
            this.o[template] = tplText;
            break;
          case 'markdown':
            this.o[template] = d._w(markdown.toHTML(tplText));
            break;
          case 'smarty':
            this.o[template] = d._w(new jSmart(tplText));
            break;
          case 'mustache':
            this.t[template] = tplText;
            break;
          case 'hogan':
            //var scan = Hogan.scan(tplText); console.log('SCAN', scan);
            //var tree = Hogan.parse(scan); console.log('TREE', tree);
            this.o[template] = d._w(Hogan.compile(tplText));
            break;
          case 'handlebars':
            this.o[template] = d._w(Handlebars.compile(tplText, {
              srcName:true
            }));
            //console.log('COMPILED', this.o[template].toString());
            break;
          case 'nunjucks':
            this.o[template] = d._w(nunjucks.compile(tplText));
            //console.log('COMPILED', this.o[template].toString());
            break;
          case 'tempo':
            this.o[template] = d._w(Tempo.prepare(template));
            break;
          case 'underscore':
            this.o[template] = d._w(_.template(tplText));
            break;
        };

      },
      /***
       * private function _l
       * for loading
       */
      _l:function(template, fThen){
        var url = template;
        switch(this.engine){
          case 'smarty':
            url = template+'.tpl';
            break;
          case 'markdown':
            url = template+'.md';
            break;
          case 'none':
          case 'markup':
          case 'mark2':
          case 'mustache':
          case 'hogan':
          case 'handlebars':
          case 'underscore':
            url = template+'.html';
            break;
          case 'mark2+':
            url = template+'.txt';
            break;
          case 'nunjucks':
            url = template+'.njk';
            break;
          case 'tempo':
            this._i(template,'');
            if(typeof(fThen)=='function')fThen(this);
            return true;
            break;
        };
        var that = this;
        var d = new Date();
        $.get({
          "url":url+'?t='+d.getTime(),
          "success":function(tplText) {
            that._i(template,tplText);
            if(typeof(fThen)=='function')fThen(that);
          }
        }).fail(function() {
          if(typeof(fThen)=='function')fThen(that);
        });
      },
      /***
       * has this template?
       */
      _h:function(template){
        switch(this.engine){
          case 'handlebars':
            if ((typeof(this.o[template])=='undefined')
            &&(typeof Handlebars.templates!='undefined')
            &&(typeof Handlebars.templates[template]!='undefined')) {
              this.o[template] = Handlebars.templates[template];
            }
          break;
          case 'mustache':
            return (typeof(this.t[template])!='undefined');
          break;
        };
        return (typeof(this.o[template])!='undefined');
      },
      /***
       * remove this template
       */
      _r:function(template){
        switch(this.engine){
          case 'handlebars':
            if ((typeof Handlebars.templates!='undefined')
            &&(typeof Handlebars.templates[template]!='undefined')) {
              delete Handlebars.templates[template];
            }
          break;
          case 'mustache':
            if (typeof(this.t[template])!='undefined') {
              delete this.t[template];
            };
          break;
        };
        if (typeof(this.o[template])!='undefined') {
          delete this.o[template];
        };
      },
      /***
       * public function reset
       * to force reload template
       */
      reset:function(template){
        this._r(template);
      },
      /***
       * public function render
       * for rendering template
       */
      render:function(opt, fThen){
        if(typeof(opt.template)=='undefined')return 0;
        if(typeof(opt.RECORD)=='undefined')return 0;
        if(typeof(opt.element)=='undefined')return 0;
        //if(typeof(this.o[opt.template])=='undefined'){
        if(!this._h(opt.template)){
          var that = this;
          this._l(opt.template, function(the){
            var s=the._s(opt.template, opt.RECORD);
            fThen(opt,s);
          });
        } else {
          var s=this._s(opt.template, opt.RECORD);
          fThen(opt,s);
        };
      },
      /***
       * public function add
       * for loading template
       */
      add:function(templates){
        for(var x in templates){
          this._l(x,templates[x]);
        };
      }
    };

    //************************** inheritance:
    var extend = function(target, source) {
        var hasOwnProperty = Object.prototype.hasOwnProperty;
        for (var propName in source) {
          //console.log(propName);
            if (hasOwnProperty.call(source, propName)) {
              //console.log(propName, source);
              target[propName] = source[propName];
            }
        }
        return target;
    };

    var create = function (proto) {
          function Tmp() {}
          Tmp.prototype = proto;
          // New empty object whose prototype is proto
          return new Tmp();
    };

    var inherits = function(SubC, SuperC) {
      var subProto = create(SuperC.prototype);
      // At the very least, we keep the "constructor" property
      // At most, we keep additions that have already been made
      extend(subProto, SubC.prototype);
      subProto.super_ = SuperC.prototype;
      SubC.prototype = subProto;
    };

    //************************** /:inheritance


    var Waiting = {
      selector : $('.loader'),
      show: function(){
        this.selector.fadeIn(200);

      },
      hide: function(){
        this.selector.fadeOut(150);
      }
    };



    var paramify = function(data){
      var e = function(s){
        return encodeURIComponent(s);
      };
      var constructObject = function(data, path, level){
        var contents="";
        for(var key in data){
            if (level == 0) {
              var curpath=e(key);
            } else {
              var curpath=path+"["+e(key)+"]";
            }
            if(Object.prototype.toString.call(data[key])==='[object Object]'||data[key] instanceof Array){
                if(!(data[key] instanceof Array)||data[key].length!=0){
                    if(JSON.stringify(data[key])=="{}"){
                        contents+="&"+curpath+"={}";
                    }else{
                        contents+=constructObject(data[key], curpath, level++);
                    }
                }else{
                    contents+="&"+curpath+"=[]";
                }
            }
            else{
                contents+="&"+curpath+"="+e(data[key]);
            }
        }
        return contents;
      }
      var s = constructObject(data, '', 0).substr(1);
      //console.log('PARAMIFY', s);
      return s;
    };

/***********************************************************************/

  function Plugin(element, options){
    var that = this;
    this.element = element;
    this.elements = {};
    this.ident=element.id;
    this.templates = {};
    this.openedTabs = {};
    this.behaviors = {};
    this.lastState = '';
    this.ajaxurl = false;
    this.plugins = {}; // prototypes
    this.plugs = {}; // instances
    this.controllers = {};
    this.crosssite = false;
    this.first_time_inited = false;
    this._running_batch = false;
    this._queue = [];
    this._await = [];
    this._send_hash = true;
    this._send_empty_hash = false;
    this._allow_polling = false;
    this._routes = {
      'action':'',
      'design':'',
      'plugin':''
    };

    /***
     * function objectIsArray
     * check if object could be used as array (for sending)
     */
    this.objectIsArray = function(o) {
      for(var x in o) {
        var t = typeof(o[x]);
        if((t=='object')||(t=='function'))return false;
      };
      return true;
    },

    /***
     * function count
     * count items in variable, depending on type
     * (array, object, scalar(1) or undefined(0))
     */
    this.count = function(o) {
      var what = Object.prototype.toString.call(o);
      switch (what) {
        case '[object Array]':
          return o.length;
        break;
        case '[object Object]':
          var c = 0;
          for(var prop in o) {
            if(o.hasOwnProperty(prop)) {
              c++;
            };
          }
          return c;
        break;
        case '[object Number]':
        case '[object String]':
        case '[object Boolean]':
          return 1;
        break;
        case '[object Undefined]':
          return 0;
        break;
      };
      return 1;
    },

    /***
     * function getvar
     * returns variable from object tree,
     * determined by string with dot notation.
     */
    this.getvar = function(key, RECORD) {
      var keys = key.split('.');
      if (keys.length < 1) {
        return;
      };
      var rec = RECORD;
      for (var i = 0; i < keys.length; i++) {
        if(typeof(rec[keys[i]]) != 'undefined'){
          rec = rec[keys[i]];
        } else {
          return;
        };
      };
      return rec;
    },

    /***
     * function formData
     * returns data from form in associative array.
     * data could be filtered with simple array "filter"
     * TODO!
     */
    this.formData = function(form, filter) {
      var unindexed_array = form.serializeArray();
      var indexed_array = {};
      $.map(unindexed_array, function(n, i){
          indexed_array[n['name']] = n['value'];
      });
      if (!filter) {
        return indexed_array;
      }
      var re = {};
      for (var i=0; i < filter.length; i++) {
        if (typeof indexed_array[filter[i]] != 'undefined') {
          re[filter[i]] = indexed_array[filter[i]];
        };
      };
      re.formid = this.getDomId(form);
      return re;
    },

    this.show_label = function(element, text) {
      if (typeof(this.plugins[pluginName+'/invalidator'])=='undefined') {
        return false;
      };
      var o = this.plugins[pluginName+'/invalidator'].getInstance(this);
      o.show_label(element, text);
    },

    this.hide_label = function(element) {
      if (typeof(this.plugins[pluginName+'/invalidator'])=='undefined') {
        return false;
      };
      var o = this.plugins[pluginName+'/invalidator'].getInstance(this);
      o.hide_label(element);
    },

    this.getDomId = function(element) {
      return _jams.getDomId(element);
      /*
      var id = $(element).attr('id');
      if (typeof id == 'undefined') {
        id = this.getUniqueDomId();
        $(element).attr('id', id)
      }
      return id;
      */
    },

    this.getUniqueDomId = function() {
      return _jams.getUniqueDomId();
      /*
      var prefix = pluginName+'_automated_id_';
      var id = prefix + uniqueid;
      uniqueid++;
      return id;
      */
    },

    this.getAjaxUrl = function() {
      return _jams.ajaxurl;
    },

    this.getAwaitId = function() {
      return _jams.getAwaitId();
      /*
      var prefix = 'await_';
      var id = prefix + uniqueid;
      uniqueid++;
      return id;
      */
    },

    /***
     * function pollData
     * special ajax request, returning chunked jsons
     * during prolonged server responses.
     * Usefull for example to update progress bar.
     */
    this.pollData = function(data, url, obj){
      if (typeof window.LongPolling == 'undefined') {
        console.log('LongPolling is not loaded yet.');
        return;
      };
      //console.log('LongPolling!!!');
      $(that.element).trigger(pluginName + '-poll-data', {'data':data, 'url':url, 'obj':obj});
      /**
       * prepare data for sending on server:
       */
      var dd={
        LOCATION: String(document.location),
        BATCH: this._running_batch
      };
      for(var x in this.controllers){
        dd = this.controllers[x].getInstance(this).sending(dd);
      };
      for(var x in data){
        if(typeof(data[x])=='object'){
          if (this.objectIsArray(data[x])) {
            dd[x]=data[x];
          };
        };
        if(typeof(data[x])=='string')dd[x]=data[x];
        if(typeof(data[x])=='number')dd[x]=data[x];
        if(typeof(data[x])=='boolean')dd[x]=data[x];
      };
      //console.log('POLLING', dd);
      var o = {
        'method':"POST",
        'data':paramify(dd),
        // These are the default values:
        maxTurns: 1, // how many times request should be repeated, after previous ended.
        // Could be false, in such case request is repeated forever.
        tag: 'chunk', // tag in server response. See the server side example.
        interval: 1000, // interval to parse server response arrived so far.
        useJSON: true, // server sends JSON chunks wrapped to xml tag.
        stopped: false, // if true, class is initially stopped. Could be started with method "start".
        // Custom callbacks:
        onChunk: function(chunk, detail){
          //Waiting.show();
          that.trigger({action:'waiting/hide', 'obj':obj});
          that.returnData(chunk);
        }, // Default is to trigger JS event "longpolling-chunk"
        onProgress: function(detail){}, // Default is to trigger JS event "longpolling-progress"
        onRequest: function(detail){}, // Default is to trigger JS event "longpolling-request"
        onComplete: function(detail){
          that.trigger({action:'waiting/hide', 'obj':obj});
        }, // Default is to trigger JS event "longpolling-complete"
        onError: function(detail){
          that.trigger({action:'waiting/hide', 'obj':obj});

        }, // Default is to trigger JS event "longpolling-error"
        onAbort: function(detail){}, // Default is to trigger JS event "longpolling-abort"
        onSuccess: function(detail){
          //console.log("SUCCESS", detail);// FALLBACK:
          if (detail.chunks === 0) {
            that.returnData(detail.all);
          };
        }, // Default is to trigger JS event "longpolling-success"
        onAllDone: function(detail){} // Default is to trigger JS event "longpolling-all-done"
      };
      var ans = new LongPolling(url, o);
      //ans.start();

    },

    /***
     * function sendData
     * main ajax request
     */
    this.sendData = function(data, url, obj){
      /**
       * here it is possible to obey installed behavior
       * instead of asking server for next steps...
       */
      if(typeof(data.action)!='undefined'){
        if(typeof(this.behaviors[data.action])=='object'){
          var o = this.behaviors[data.action];
          if(typeof(o['RECORD'])=='function')o['RECORD']=this.behaviors[data.action]['RECORD'](data);
          o.data = data;
          return this.trigger(o);
        } else if(typeof(this.behaviors[data.action])=='function'){

          var o = this.behaviors[data.action];
          //console.log('BEHAVIOR run from sendData');
          //return o(data);
          return o.call(this.api(), data);
        }
      };

      if (this._allow_polling) {
        //console.log('POLLING');
        //Waiting.show();
        that.trigger({action:'waiting/show', 'obj':obj});
        this.pollData(data, url, obj);return;
      };
      //console.log('NOT POLLING');
      $(that.element).trigger(pluginName+'-send-data', {'data':data, 'url':url, 'obj':obj});

      /**
       * show them that we are waiting for response...
       * DEPRECATED!

      $('div#facebox').find('img.loading').each(function(i,element){
        $(element).css({'display':'block'});
      });
      $('div#facebox').find('div.optionButtons').each(function(i,element){
        $(element).css({'display':'none'});
      });
      */

      //Waiting.show();
      that.trigger({action:'waiting/show', 'obj':obj});

      /**
       * prepare data for sending on server:
       */
      var dd={
        LOCATION: String(document.location),
        BATCH: this._running_batch
      };
      for(var x in this.controllers){
        dd = this.controllers[x].getInstance(this).sending(dd);
      };
      for(var x in data){
        if(typeof(data[x])=='object'){
          if (this.objectIsArray(data[x])) {
            dd[x]=data[x];
          };
        };
        if(typeof(data[x])=='string')dd[x]=data[x];
        if(typeof(data[x])=='number')dd[x]=data[x];
        if(typeof(data[x])=='boolean')dd[x]=data[x];
      };

      //console.log(dd);

      var oo = {
        type:'POST',
        dataType:'json',
        url:url,
        data:dd,
        success:function(re, status, xhr){
          $(that.element).trigger(pluginName+'-return-data-success', xhr, status);
          /**
           * show them that our waiting was ended,
           * and do the job...
           */
          //console.log('COMPLETE', re);
          that.returnData(re);
        },
        error:function(xhr, status, errorThrown){
          $(that.element).trigger(pluginName+'-return-data-error', xhr, status, errorThrown);
        },
        complete:function(xhr, status){
          $(that.element).trigger(pluginName+'-return-data-complete', xhr, status);
          /**
           * show them that our waiting was ended...
           */
          //Waiting.hide();

          that.trigger({action:'waiting/hide', 'obj':obj});
        }
      };
      if (this.crosssite) {
        oo.type='GET';
        oo.crossDomain = true;
      };
      /**
       * do the call...
       */
      $.ajax(oo);

    },

    /***
     * function returnData
     * main ajax response
     */
    this.returnData = function(data){
      //console.log(data);
      $(that.element).trigger(pluginName+'-return-data', data);
      if(typeof(data.action)!='undefined'){
        this.trigger(data);
      } else if(typeof(data.message)=='string'){
        //console.log('MESSAGE', data.message);

      };
    },
    /**
     * internal method _settle_elements
     *
     */
    this._dispatch_elements = function(id) {
      var elements = {};
      if (typeof id == 'object') {
        elements.main = id;
      } else if (typeof id == 'string') {
        elements.main = $('#' + id.replace(/^#/, ''));
      } else {
        elements.main = $(this.element);
      }
      return elements;
    },
    this._dispatch_root_element = function(elements, plugin) {
      if (typeof(plugin.root)!='undefined') {
        if (plugin.root == '') {
          plugin.root = 'document';
        }
        if (typeof elements[plugin.root] == 'undefined') {
          elements[plugin.root] = $(plugin.root);
        };
        return elements[plugin.root];
      };
      return elements.main;
    },

    /***
     * function invalidate
     *
     */
    this.invalidateTemplate = function(id,RECORD){
      var elements = this._dispatch_elements(id);
      var waxedInvalidation = false;

      $(elements.main).find('label.waxed-invalidation').each(function(i, element){
        waxedInvalidation = true;
        $(element).html('');
      });

      $(elements.main).find('span.waxed-invalidation').each(function(i, element){
        waxedInvalidation = true;
        $(element).html('');
      });

      $(elements.main).find('label.invalid').each(function(i,element){
        $(element).remove();
      });

      $(elements.main).find('input,textarea').each(function(i,element){
        $(element).removeClass('invalid');
      });

      //console.log('waxedInvalidation', waxedInvalidation);

      for (var x in this.plugins) {
        var elem = this._dispatch_root_element(elements, this.plugins[x]);
        var search = this.plugins[x].search;
        $(elem).find(search).each(function(i,element){
          var dd = $(element).data();
          var o = that.plugins[x].getInstance(that, element, dd);
          o.invalidate(RECORD);
        });
      };
      /*
      for(var x in RECORD){
        //console.log(x);
        $(elements.main).find('input[name='+x+']').each(function(i,element){
          if($(element).attr('type')!='hidden'){
            $(element).addClass('invalid');
            if (waxedInvalidation) {
              var label = $(element).closest('div').find('.waxed-invalidation').data('label');
              if (typeof(label)=='undefined') {
                label = $(element).closest('div').find('label').first().text();
              };
              $(element).closest('div').find('.waxed-invalidation').html(RECORD[x].replace('{label}', label));

            } else {
              if(!$(element).hasClass('waxed-plugin-input')) {
                $(element).after('<label class="invalid invalid-feedback" style="display:block;" >'+RECORD[x]+'</label>');
              }
              //$(element).before('<div class="invalid-feedback" style="display:block;" >'+RECORD[x]+'</div>');
              //$(element).addClass('is-invalid');
            };
          };
        });

        $(elements.main).find('textarea[name='+x+']').each(function(i,element){
          $(element).addClass('invalid');
            if (waxedInvalidation) {
              $(element).closest('div').find('.waxed-invalidation').html(RECORD[x]);

            } else {
              $(element).after('<label class="invalid">'+RECORD[x]+'</label>');
            };
        });

        $(elements.main).find('select[name='+x+']').each(function(i,element){
          $(element).addClass('invalid');
            if (waxedInvalidation) {
              $(element).closest('div').find('.waxed-invalidation').html(RECORD[x]);

            } else {
              $(element).after('<label class="invalid">'+RECORD[x]+'</label>');
            };
        });

        $(elements.main).find('button[name='+x+']').each(function(i,element){
          $(element).addClass('invalid');
            if (waxedInvalidation) {
              $(element).closest('div').find('.waxed-invalidation').html(RECORD[x]);

            } else {
              $(element).after('<label class="invalid">'+RECORD[x]+'</label>');
            };
        });

      };
      */

    },

    /***
     * function redraw
     * special case without initialization...
     *
     */
    this.redraw = function(id, RECORD){
      var elements = this._dispatch_elements(id);
      for (var x in this.plugins) {
        var elem = this._dispatch_root_element(elements, this.plugins[x]);
        var search = this.plugins[x].search;
        $(elem).find(search).each(function(i,element){
          var dd=$(element).data();
          if ((typeof(dd.jam)!='undefined') && (dd.jam != that.ident)) {
            return false;
          };
          var o = that.plugins[x].getInstance(that, element, dd);
          o.setRecord(RECORD);
        });
      };
    },

    /***
    * function freeTemplate
    *
    */
    this.freeTemplate = function(id){
      var elements = this._dispatch_elements(id);
      //console.log('free', elements);
      for (var x in this.plugins) {

        //var elem = elements.main;
        var search = this.plugins[x].search;
        //console.log(search);
        //if (typeof(this.plugins[x].root)!='undefined') {
          //continue; // dont free objects outside the scope.
        //};
        // searching objects only inside the scope:
        $(elements.main).find(search).each(function(i,element){
          //console.log(element);
          var dd=$(element).data();
          //console.log(x, element);
          if ((typeof(dd.jam)!='undefined') && (dd.jam != that.ident)) {
            return false;
          };
          if (typeof that.plugins[x].freeInstance == 'function') {
            that.plugins[x].freeInstance(x, element);
          }
        });
      }
    }
    /***
    * function initTemplate
    *
    */
    this.initTemplate = function(id, RECORD){
      var elements = this._dispatch_elements(id);
      $(elements.main).trigger(pluginName+'-template-init', RECORD);
      var instances = [];
      for (var x in this.plugins) {
        var elem = this._dispatch_root_element(elements, this.plugins[x]);
        var search = this.plugins[x].search;
        //console.log(x, search);
        if (typeof(this.plugins[x].reset)=='function') {
          this.plugins[x].reset(id);
        };
        if ($(elem).is(search)) {
          var dd=$(elem).data();
          if ((typeof(dd.jam)!='undefined') && (dd.jam != that.ident)) {
            return false;
          };
          var o = that.plugins[x].getInstance(that, elem, dd, id);
          instances.push(o);
          //o.setRecord(RECORD);
          if(typeof(dd.ident)=='string'){
            that.elements[dd.ident]=o;//{o:element,t:'tabledit'};
          };
        };
        $(elem).find(search).each(function(i,element){
          $(element).find('div').each(function(ii,ee){
            var id = $(ee).attr('id');
            if (typeof id !== 'undefined') {

            };
            //console.log('ADDEDID', ee, id);
          });

          var dd=$(element).data();
          //console.log(x, element, dd);
          if ((typeof(dd.jam)!='undefined') && (dd.jam != that.ident)) {
            return false;
          };
          var o = that.plugins[x].getInstance(that, element, dd, id);
          //console.log(x, o);
          instances.push(o);
          //o.setRecord(RECORD);
          if(typeof(dd.ident)=='string'){
            that.elements[dd.ident]=o;//{o:element,t:'tabledit'};
          };
        });
      };
      //console.log('INSTANCES', instances);
      //console.log('ELEM', elem);
      //console.log('ID', id);
      for (var i=0; i<instances.length; i++) {
        instances[i].setRecord(RECORD);
      };
      //console.log(elements);

      /**
       * change behavior for anchors with ".action" class.
       * Click now sends data through ajax instead of page redirection.
       */
      $(elements.main).find('a.action, button.action,a.waxed-action, button.waxed-action').off().on('click', function(ev){
        var data=$(ev.currentTarget).data();
        
        if(typeof(data.action)=='undefined'){
          return true;
        };
        ev.preventDefault();
        var url = $(ev.currentTarget).attr('href');
        //console.log('INIT TEMPLATE',url,data);
        //console.log('click', url, typeof(url));
        if((typeof(url)=='undefined')||(url=='')){
          if(typeof(data.action)!='undefined'){
            //console.log(typeof(that.behaviors[data.action]),that.behaviors[data.action]);
            //console.log(that.behaviors);
            //console.log(data.action);
            //console.log(that);
            if(typeof(that.behaviors[data.action])=='object'){

              var o = that.behaviors[data.action];
              if(typeof(o['RECORD'])=='function')o['RECORD']=that.behaviors[data.action]['RECORD'](data);
              if(typeof(o['RECORD'])=='undefined')o.RECORD = data;
              return that.trigger(o);
            } else if(typeof(that.behaviors[data.action])=='function'){
              var o = that.behaviors[data.action];
              //return o(data);
              //console.log('BEHAVIOR run from onclick', o.toString());

              return o.call(that.api(), data);
            } else if ($(ev.currentTarget).hasClass('submiter')) {
              var fr = $(ev.currentTarget).closest('form');
              $(fr).find('input[name=action]').val(data.action);
              $(fr).submit();


            };
            that.trigger(data);
          }
        } else {
          data.ELEMID = that.getDomId(ev.currentTarget);
          that.sendData(data, url, {'element':ev.currentTarget});
        };
      });

      /**
       * change behavior for table td with ".action" class.
       * Click now sends data through ajax.
       */
      $(elements.main).find('td.action').click(function(ev){
        ev.preventDefault();
        var data=$(ev.currentTarget).data();
        var url = data.href;
        that.sendData(data, url, {'element':ev.currentTarget});
      });

      /**
       * change behavior for all forms, except of that with ".ownLogic" class.
       * Submit now sends data through ajax instead of page redirection.
       */
      $(elements.main).find('form').each(function(i,element){
        var tg = $(element).attr('target');
        if(typeof(tg)!='undefined'){
          return 0;
        };
        if($(element).hasClass('ownLogic')){
          return false;
        };
        var dd=$(element).data();
        if(typeof(dd.ident)=='string'){
          that.elements[dd.ident]={o:element,t:'form'};
        };
        $(element).on('submit', function(ev){
          ev.preventDefault();

          $(element).find('img.loading').each(function(i,element){
            $(element).css({'display':'block'});
          });

          //console.log(ev.currentTarget);
          var d = $(ev.currentTarget).serializeArray();
          var data2 = that.formData($(ev.currentTarget));
          //var data = Object.fromEntries();


          var data = {};
          for(var i=0;i<d.length;i++){
            if((typeof(data[d[i].name])=='string')||(typeof(data[d[i].name])=='number')||(typeof(data[d[i].name])=='boolean')){
              data[d[i].name] = [data[d[i].name]];
            };
            if((typeof(data[d[i].name])=='object')){
              data[d[i].name].push( d[i].value );
            } else {
              data[d[i].name] = d[i].value;
            };
          };

          //console.log('FORM_DATA', data, data2);

          data.FORMID = that.getDomId(ev.currentTarget);
          var url = $(ev.currentTarget).attr('action');
          that.sendData(data, url, element);
        });
      });

      /**
       * Set focus on first input with ".focus" class in form...
       */
      $(elements.main).find('form input.focus').each(function(i,element){
        if(i==0){
          $(element).focus();
        };
      });

      $(elements.main).find('.boxload').each(function(i, element) {
        var dd = $(element).data();
        if (typeof(dd.url) == 'string') {
              var oopt = {
                'element':element,
                'RECORD':RECORD,
                'template':dd.url
              };
              Template.render(oopt, function(oopt, s){
                that.freeTemplate(oopt.element);
                $(oopt.element).html(s);
                $(oopt.element).trigger(pluginName+'-boxload', s);
                that.initTemplate(oopt.element, oopt.RECORD);
              });
        };
      });

      if (!this.first_time_inited) {
        this.trigger({
          action:'template/first/inited'
        });
        this.first_time_inited=true;
        $(elements.main).trigger(pluginName+'-template-inited', true);
      } else {
        this.trigger({
          action:'template/inited'
        });
        $(elements.main).trigger(pluginName+'-template-inited', false);
      }
      return elements.main;
    },

    /***
    * internal function _getPageScroll
    *
    */
    this._getPageScroll = function() {
      var xScroll, yScroll;
      if (self.pageYOffset) {
        yScroll = self.pageYOffset;
        xScroll = self.pageXOffset;
      } else if (document.documentElement && document.documentElement.scrollTop) {     // Explorer 6 Strict
        yScroll = document.documentElement.scrollTop;
        xScroll = document.documentElement.scrollLeft;
      } else if (document.body) {// all other Explorers
        yScroll = document.body.scrollTop;
        xScroll = document.body.scrollLeft;
      }
      return new Array(xScroll,yScroll)
    },

    /***
    * internal function _fullscreen
    *
    */
    this._fullscreen = function(opt) {
      var w = $(window).width();
      var h = $(window).height();
      var scroll = this._getPageScroll();
      $('body').css({
        'overflow':'hidden',
        'width':w+'px', 'height':h+'px'
      });
      $('body').append('<div id="fullscreen"><div>zatvoriLA nA?h�lad</div><iframe /></div>');
      var w = $(window).width();
      var h = $(window).height();
      $('#fullscreen').css({
        'position':'absolute','z-index':1002,
        'width':w+'px', 'height':h+'px',
        'top': scroll[1], 'left': scroll[0],
        'background-color':'white',
        'overflow':'hidden','opacity':.1
      });
      var ifr = $('#fullscreen').find('iframe')[0];
      var pan = $('#fullscreen').find('div')[0];
      $(pan).css({
        'width':'100%','height':'20px','overflow':'hidden','margin':'0px',
        'background-color':'yellow',
        'cursor':'pointer',
        'text-align':'center'
      });
      $(ifr).css({'width':'100%','height':(h-20)+'px','margin':'0px'});
      $(ifr).attr('src',opt.RECORD.url);
      $('#fullscreen').animate({'opacity':1},500);
      $(pan).click(function(){
        $('#fullscreen').remove();
        $('body').css({
          'overflow':'auto',
          'width':'auto', 'height':'auto'
        });
      });
    },

    /***
    * internal function _clear
    *
    */
    this._clear = function(opt) {
      $(opt.element).html('');
    },

    /***
    * internal function _display
    *
    */
    this._display = function(opt, callback) {
      var cl = '';
      if (typeof(opt.class)!='undefined') {
        cl = opt.class;
      };
      Template.render(opt, function(opt, s) {
        if (opt.element=='facebox') {
          if (typeof $().modal == 'function') {
            //$('#dialog').modal({keyboard: false});
            var o=document.jammin[pluginName+'/bootstrap_modal'].getInstance(that, $('body'));
            o.setTemplate(s);
            var el = that.initTemplate('dialog-modal-body', opt.RECORD);
          } else {
            $.facebox(String(s), cl);
            /**
             * enliven plugged elements inside...
             */
            var el = that.initTemplate('facebox', opt.RECORD);
          };


        } else {
          /**
           * its possible to append to previously rendered...
           */
          if (typeof(opt.append)!='undefined') {
            var elAppend = $(s).appendTo(opt.element)[0];
          } else if (typeof(opt.prepend)!='undefined') {
            var elAppend = $(s).prependTo(opt.element)[0];
          } else {
            that.freeTemplate(opt.element);
            if (cl!='') {
              $(opt.element).addClass(cl);
            };
          /**
           * add some elegance...
           */
            if (defaults.fade_in_time>0) {
              $(opt.element).css({opacity:0.1});
              $(opt.element).html(s);
              $(opt.element).animate({opacity:1}, defaults.fade_in_time);
            };
          };
          /**
           * enliven plugged elements inside...
           */
          if (elAppend) {
            //console.log('elAppend', elAppend);
            var el = that.initTemplate(elAppend, opt.RECORD);// only init that appended part!
          } else if(typeof(opt.element)=='string'){
            var el = that.initTemplate($('#'+opt.element.replace(/^#/, '')).attr('id'), opt.RECORD);
          } else {
            var el = that.initTemplate(opt.element, opt.RECORD);
          }
        };
        if (typeof callback == 'function') {
          callback(el);
        };
      });
    },

    /***
    * internal function _dialog
    *
    */
    this._dialog = function(opt, callback) {
      if(typeof(document.jammin[pluginName+'/dialog'])=='undefined'){
        console.log('Plugin for dialog is required.');
        return false;
      };

      var o = document.jammin[pluginName+'/dialog'].getInstance(that, $('body'), $('body').data());
      o.open(opt);
      if (typeof opt.signature == 'undefined') {
        opt.signature = 'dialog-' + (uniqueid++);
      };
      if (typeof callback == 'function') {
        callback(o);
      };
      /*
      opt.RECORD._dialog_signature_ = opt.signature;
      Template.render(opt, function(opt, s) {
        var el = o.setTemplate(s, opt);
        if (typeof callback == 'function') {
          callback(el);
        };
      });
      */
    },

    /***
    * internal function _dialogOpen
    * for loading additional plugins on behalf...
    */
    this._dialogOpen = function(opt, callback) {
      if(typeof(document.jammin[pluginName+'/dialog'])=='undefined'){
        console.log('Plugin for dialog is required.');
        return false;
      };
      if (typeof opt.signature == 'undefined') {
        opt.signature = 'dialog-' + (uniqueid++);
      };
      var o = document.jammin[pluginName+'/dialog'].getInstance(that, $('body'), $('body').data());
      var el = o.open(opt);
      if (typeof callback == 'function') {
        callback(el);
      };
      return el;
    },

    /***
    * internal function _dialogClose
    *
    */
    this._dialogClose = function(signature) {
      if(typeof(document.jammin[pluginName+'/dialog'])=='undefined'){
        return false;
      };
      var o=document.jammin[pluginName+'/dialog'].getInstance(this, $('body'), $('body').data());
      o.close(signature);
      return true;
    },

    /***
    * internal function _dialogModal
    *
    */
    this._dialogModal = function(bYes) {
      if(typeof(document.jammin[pluginName + '/bootstrap_modal'])!='undefined'){
        var o=document.jammin[pluginName + '/bootstrap_modal'].getInstance(this, $('body'), $('body').data());
        o.setModal(bYes);
        return true;
      };
    },

    this.clearClosing = function() {
      if((typeof(this.toClosing)!='undefined')&&(this.toClosing)){
          clearTimeout(this.toClosing);
          this.toClosing=false;
      };
    }

    /***
     * internal function setBehavior
     *
     */
    this.setBehavior = function(on, opt){
      if (opt === false) {
        delete this.behaviors[on];
      } else {
        if (typeof opt == 'string') {
          this.behaviors[on] = Function('var API=arguments[0];return function(){' + opt + ';}')(this.api());
          //this.behaviors[on] = Function(opt)();
        } else {
          this.behaviors[on] = opt;
        }
      }
      //console.log('behaviors', this.behaviors);
    },

    /***
     * internal function hashState
     *
     */
    this.hashState = function(s){
      hashLastState = s;
      document.location.hash = s;
    },

    /***
     * internal function hashChange
     *
     */
    this.hashChange = function(hash) {
      if (!this._send_hash) {
        return false;
      };
      $(this.element).trigger(pluginName + '-hash-change', hash);
      if (!this.ajaxurl) return false;
      if(hash=='#'+hashLastState)return false;
      var s = hash.slice(1);
      if (s[0] == '!') s = s.slice(1);
      this._dialogClose();
      if (!this._send_empty_hash) {
        if(s=='')return false;
      };
      var o={
        'hash':s,
        'action':'hash/change'
      };
      //hashLastState = '!'+s;
      hashLastState = s;
      this.sendData(o, this.ajaxurl, that.element );
      return true;
    },

    this.visible = function (element, fullyInView) {
        var pageTop = $(window).scrollTop();
        var pageBottom = pageTop + $(window).height();
        var elementTop = $(element).offset().top;
        var elementBottom = elementTop + $(element).height();
        //console.log('isOnScreen', pageTop, pageBottom, elementTop, elementBottom, element);

        if (fullyInView === true) {

          var re = ((pageTop < elementTop) && (pageBottom > elementBottom));
        } else {
          var re = ((elementTop <= pageBottom) && (elementBottom >= pageTop));
        }
        return re;
    },

    this.findPluginInstances = function(pluginName, element) {
      if (typeof(this.plugins[pluginName]) == 'undefined') {
        return [];
      }
      if (typeof element == 'undefined') {
        element = this.element;
      }
      var search = this.plugins[pluginName].search;
      var a = [];
      $(element).each(function(i, element) {
        if (($(element).is(search)) && ($(element).data('plugin_' + pluginName))) {
          a.push($(element).data('plugin_' + pluginName));
        };
        $(element).find(search).each(function(i, o) {
          if ($(o).data('plugin_' + pluginName)) {
            a.push($(o).data('plugin_' + pluginName));
          }
        });
      });
      return a;
    },

    this.plugFree = function(plugid){
      delete this.plugs[plugid];
    },
    this.plugRegister = function(o){
      var pid = '_' + (plugid)++;
      this.plugs[pid] = o;
      return pid;
    },
    this.plugExtend = function(plug, _api, plugName) {

      if (typeof plug.prototype.super_ != 'undefined') {
        return;
      }

      if (typeof _api == 'undefined') {
        _api = [];
      }
      var a = ['setRecord', 'getRecord', 'invalidate', 'free', '_'];
      _api = _api.filter(function(f){
        return (a.indexOf(f) < 0);
      }).concat(a);

      if (typeof plug.prototype.super_ == 'undefined') {

        if (typeof plug.prototype.api == 'function') {
          console.log('method api should not be defined!');
        };

        var SuperPlug = function(pluggable,element,dd) {};
        SuperPlug.prototype[pluginName] = function(action, options) {
            var opt = {};
            if (typeof action == 'object') {
              $.extend(opt, action);
            };
            if (typeof options == 'object') {
              $.extend(opt, options);
            };
            //at last:
            if (typeof action == 'string') {
              opt.action = action;
            };
            return this.pluggable.trigger.call(this.pluggable, opt);
        };
        SuperPlug.prototype.getplugid = function() {
          //console.log('IDget', this);
          return this.plugid;
        };
        SuperPlug.prototype.setRecord = function() {
          //console.log('SUPER.SETRECORD', this);
        };
        SuperPlug.prototype.getRecord = function() {
          //console.log('SUPER.GETRECORD', this);
        };
        SuperPlug.prototype._init_ = function() {
          if (!this.plugid) {
            this.plugid = this.pluggable.plugRegister(this);
            this.init();
          } else {
            //console.log('DOUBLE.INIT!!!', this.plugid);// THROW!
          }
        };
        SuperPlug.prototype._free_ = function() {
          this.pluggable.plugFree(this.plugid);
        };
        SuperPlug.prototype._test = function() {
        };
        SuperPlug.prototype._ = function(element, plugName, callback) {
          var a = this.pluggable.findPluginInstances(plugName, element);
          $(a).each(function(){
            callback.apply(that, arguments);
          });
          return a;
        };
        SuperPlug.prototype._api_ = function() {
          var a = {}; var that = this;
          var createfunc = function(f, sf) {
            if (typeof sf == 'function') {
              return function() {var r = f.apply(that, arguments); sf.apply(that); return r;};
            }
            return function() {return f.apply(that, arguments);};
          }
          for (var i = 0; i < _api.length; i++) {
            if (typeof this[_api[i]] == 'function') {
              if (_api[i] == 'free') {
                a[_api[i]] = createfunc(this[_api[i]], this['_free_']);
              } else if (_api[i] == 'setRecord') {
                a[_api[i]] = createfunc(this[_api[i]], this.super_[_api[i]]);
              } else {
                a[_api[i]] = createfunc(this[_api[i]]);
              }
            }
          };
          if (typeof a.free != 'function') {
            a.free = createfunc(this['_free_']);
          };
          a.$ = $(this.element);
          return a;
        };
        inherits(plug, SuperPlug);
      };
    },

    /***
     * internal function loadPlugins
     * Loads only plugins, which are not loaded yet.
     * Instantiate slot for plugins in public space, if not exists yet.
     */
    this.loadPlugins = function(){
      if (typeof(document.jammin) == 'undefined') {
        document.jammin = {};
      } else {
        for(var x in document.jammin) {
          if (typeof(this.plugins[x]) == 'undefined') {
            var plugin = this.plugins[x] = document.jammin[x];
            $(this.element).trigger(pluginName+'-plugin', x);
            if (typeof(this.plugins[x].freeInstance) == 'undefined') {
              this.plugins[x].freeInstance = function(pluginName, elem) {
                var data = $.data(elem);
                if(!data['plugin_'+pluginName])return false;
                $(elem).trigger(pluginName+'-plugin-instance-free', pluginName);
                var o = data['plugin_'+pluginName];
                if (typeof o.free == 'function') o.free();
                $(elem).data('plugin_'+pluginName, null);
              };
            };
          };// adding plugin end
        };
      };
    },

    /***
     * public function init
     * initialization
     */
    this.init=function(){
      // register instance api:
      //_jams.addApi('#' + this.getDomId(this.element), this.api(), options);
      _jams.addApi(this.element, this.api(), options);
      //*********************************************************

      var data = $(this.element).data();
      var o = {};
      var f = false;
      if (typeof(data.ajaxurl) != 'undefined') {
        o.ajaxurl = data.ajaxurl;
        f = true;
      };
      if (typeof(data.crosssite) != 'undefined') {
        o.crosssite = data.crosssite;
        f = true;
      };
      if (typeof(data.sendhash) != 'undefined') {
        o.sendhash = Number(data.sendhash);
        f = true;
      };
      if (typeof(data.polling) != 'undefined') {
        o.polling = Number(data.polling);
        f = true;
      };
      if (typeof(data.routes) != 'undefined') {
        o.routes = Number(data.routes);
        f = true;
      };
      if (f) {
        this.trigger(o);
      };

      //*********************************************************
      /*
      $(window).unload(function() {
        $('body').animate({opacity:0.1}, 500)
      });
      */
      // register loaded yet scripts:
      if (_jams.count() < 2) {
        $(document).find('script').each(function(i, o){
          if (($(o).attr('type')=='text/javascript')&&($(o).attr('src'))) {
            var src = $(o).attr('src');
            if (_loaded.js.indexOf(src) < 0) {
              _loaded.js.push(src);
            };
          };
        });
      }

      this.loadPlugins();

      var hash = document.location.hash;

      this.trigger(options);

      $(window).bind('hashchange', function() {
        that.hashChange(document.location.hash);
      });

      setTimeout(function(){
        that.hashChange(hash);
      }, 300);

      // trigger jquery event:
      $(this.element).trigger(pluginName+'-inited', this.element.id);

    },

    /***
     * Internal
     */
    this._reg = function(callback) {
      var await_id = this.getAwaitId();
      this._await.push(await_id);
      //console.log('AWAIT-START', await_id);
      return function() {
        that._done(await_id);
        //console.log('AWAIT-END', await_id);
        if (typeof callback == 'function') {
          callback.apply(that, arguments);
        };
      };
    },

    this._done = function(await_id) {
      this._await = this._await.filter(function(a){
        return a != await_id;
      });
      this.trigger();
    },

    this.queuePercent = function() {
      var max = 0;
      return {
        get: function() {
          var L = that._queue.length;
          max = Math.max(max, L);
          return 100 - Math.floor((L/max) * 100);
        },
        reset: function() {
          max = 0;
        }
      };
    }(),

    /***
     * public method trigger
     * Main dispatch of commands.
     * Commands could be triggered either
     * - from jscript during initialization,
     * - or from server via ajax,
     * - or from plugins,
     * - or from installed behaviors.
     */
    this.trigger = function(opt){
      //console.log('AWAIT-TRIG', opt);
      if (typeof opt == 'object') {
        this._queue.push(opt);
      }
      //console.log('TRIG', this._queue);

      if (this._await.length > 0) {
        //console.log('AWAIT', this._await);
        return false;
      };
      //console.log('queue', this._queue);
      if(this._queue.length > 0) {
        opt = this._queue.shift();
      };
      if (typeof opt != 'object') {
        return this;
      }
      //console.log('TRIG:go', opt);

      /**
       * this is case to pick another root...
       */
      if ((typeof(opt.pick)=='string') || (typeof(opt.pick)=='object')) { // && (typeof (_jams[opt.pick]) != 'undefined')
        var pickid = opt.pick;
        if (typeof(opt.pick)=='object') {
          pickid = '#'+this.getDomId(pickid);
          if (typeof(opt.RECORD)=='object') {
            opt.RECORD.pickid = pickid;
          };
        };
        var jj = _jams.getApi(pickid);//$(opt.pick).data('plugin_jam');
        if (typeof jj == 'undefined') {
          //console.log('PICKLED',$(opt.pick),opt.pick);
          $(opt.pick)[pluginName]({
            'routes': this._routes,
            'polling': this._allow_polling,
            'pickid': pickid
          });
          //console.log('PICK', $(opt.pick));
          jj = _jams.getApi(pickid);
          //console.log('PICK', opt.pick, pickid, jj, _jams);
        };
        if (typeof jj != 'undefined') {
          delete opt.pick;
          jj.trigger(opt);
        } else {
          //console.log('!!!PICK AWAIT');
          if (typeof opt.repeat == 'undefined') {
            opt.repeat = 0;
          }
          opt.repeat++;
          if (opt.repeat < 4) setTimeout(function(){
            that.trigger(opt);
          }, 100);
        };
        this._done();
        return this;
      };

      if(typeof(opt.action)=='undefined')opt.action='load';
      if(typeof(opt.engine)!='undefined')Template.setEngine(opt.engine);
      if(typeof(opt.ajaxurl)!='undefined')this.ajaxurl = opt.ajaxurl;
      if(typeof(opt.crosssite)!='undefined')this.crosssite = opt.crosssite;
      if(typeof(opt.polling)!='undefined')this._allow_polling = opt.polling;

      if(typeof(opt.routes)!='undefined') {
        if(typeof(opt.routes.action)!='undefined') {
          this._routes.action = opt.routes.action;
          this.ajaxurl = opt.routes.action;
        };
        if(typeof(opt.routes.design)!='undefined') {
          this._routes.design = opt.routes.design;
        };
        if(typeof(opt.routes.plugin)!='undefined') {
          this._routes.plugin = opt.routes.plugin;
        };
      }

      if (typeof(opt.sendhash) == 'number') {
        this._send_hash = (opt.sendhash&1) != 0;
        this._send_empty_hash = (opt.sendhash&2) != 0;
      }

      $(this.element).trigger(pluginName+'-trigger', opt);

      if(typeof(this.behaviors[opt.action])=='function'){
        //console.log('BEHAVIOR run from trigger');
        this.behaviors[opt.action].call(this.api(), opt);
        this._done();
        return this;
      };

      var appendfunc = function(f) {
        var createfunc = function(f) {
          return function() {return f.apply(that, arguments);};
        };
        that._queue = that._queue.concat([{
          'action':'func',
          'func':createfunc(opt.callback)
        }]);
      };

      switch(opt.action){

        case 'waiting/show':
          //console.log(opt.action,opt);
          if ((typeof opt.obj == 'object')&&(typeof opt.obj.element == 'object')) {
              $(opt.obj.element).trigger('waxed-waiting-show');
          } else {
              $(document.body).trigger('waxed-waiting-show');
          }
          break;
        case 'waiting/hide':

          //console.log(opt.action,opt);
          //console.log('PARENT',$(opt.obj.element).parent());
          if ((typeof opt.obj == 'object')
            &&(typeof opt.obj.element == 'object')
            &&($(opt.obj.element).parent()[0])
            ) {
              //console.log('PARENT',$(opt.obj.element).parent());
              $(opt.obj.element).trigger('waxed-waiting-hide');
          }
          $(document.body).trigger('waxed-waiting-hide');
          break;

        /**
         * it is possible to enlive existing plugged elements without loading template...
         */
        case 'inspire':
            //console.log('INSPIRE', this.element);
            if(typeof(opt.element)=='undefined')opt.element = this.element;
            if(typeof(opt.element)=='string'){
              //console.log(opt.element);
              var ooo = $(opt.element);
              if (ooo.length<1) return that;
              opt.element = ooo[0];
              //console.log(opt.element);
            };
            //console.log('INSPIRING', opt.element);
            if(typeof(opt.RECORD)=='undefined')return that;
            if(typeof(opt.ontime)!='undefined'){
              setTimeout(function(){
                that.initTemplate(opt.element, opt.RECORD);
              },opt.ontime);
            } else {
              that.initTemplate(opt.element, opt.RECORD);
            };
          break;

        /**
         * altough main functionality is to DISPLAY template...
         */
        case 'display':
            if (typeof(opt.callback)=='function') {
              //appendfunc(opt.callback);
            } else {
              opt.callback = null;
            }

            if(typeof(opt.element)=='undefined')opt.element = this.element;
/*** ??? ***/
            if(typeof(opt.element)=='string'){
              //console.log(opt.element);
              var ooo = $(opt.element);
              if (ooo.length<1) return that;
              opt.element = ooo[0];
              //console.log(opt.element);
            };
/*** /??? ***/
            if(typeof(opt.template)=='undefined')return this;
            if(typeof(opt.RECORD)=='undefined')return this;
            if(typeof(opt.ontime)!='undefined'){
              setTimeout(function(){
                that._display(opt, that._reg(opt.callback));
              },opt.ontime);
            } else {
              this._display(opt, this._reg(opt.callback));
            };
            return this;
          break;

        /**
         * or to show template in DIALOG window...
         */
        case 'dialog':
            if (typeof(opt.callback)=='function') {
              //appendfunc(opt.callback);
            } else {
              opt.callback = null;
            }

            if(typeof(opt.template)=='undefined')return this;
            if(typeof(opt.RECORD)=='undefined')return this;
            opt.element='facebox';
            this._dialog(opt, that._reg(opt.callback));
            if(typeof(opt.timeout)!='undefined'){
              this.toClosing=setTimeout(function(){
                that._dialogClose();
              },Number(opt.timeout));
            };
            return this;
          break;

        /**
         * or to open blank DIALOG window...
         */
        case 'dialog/open':
            if (typeof(opt.callback)=='function') {
              //appendfunc(opt.callback);
            } else {
              opt.callback = null;
            }
            return this._dialogOpen(opt, that._reg(opt.callback));
          break;

        /**
         * dialog can be closed...
         */
        case 'dialog/close':
        case 'dialogClose':
          var signature = '';
          if(typeof(opt.signature)!='undefined'){
            signature = opt.signature;
          };

            if(typeof(opt.timeout)!='undefined'){
              this.toClosing=setTimeout(function(){
                that._dialogClose(signature);
              },Number(opt.timeout));
            } else {
              this._dialogClose(signature);
            };
          break;

        /**
         * dialog can be set to modal...
         */
        case 'dialog/modal':
            this._dialogModal(true);
          break;

        case 'dialog/free':
            this._dialogModal(false);
          break;

        case 'title':
          //console.log('TITLE');
            if(typeof(opt.title)!='undefined'){
              if(typeof(opt.ontime)!='undefined'){
                setTimeout(function(){
                  document.title = opt.title;
                },Number(opt.ontime));
              } else {
                document.title = opt.title;
              }
            };
          break;

          case 'favicon':
            var faviconChange = function(href) {
              var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
              link.type = 'image/x-icon';
              link.rel = 'shortcut icon';
              link.href = href;
              document.getElementsByTagName('head')[0].appendChild(link);
            };
              if(typeof(opt.icon)!='undefined'){
                if(typeof(opt.ontime)!='undefined'){
                  setTimeout(function(){
                    faviconChange(opt.icon);
                  },Number(opt.ontime));
                } else {
                  faviconChange(opt.icon);
                }
              };
            break;

        case 'submit':
            if(typeof(opt.timeout)!='undefined'){
              setTimeout(function(){
                $('#'+opt.formid).submit();
              },Number(opt.timeout));
            } else {
              $('#'+opt.formid).submit();
            };
          break;

        /**
         * also it is possible to display template in FULLSCREEN...
         */
        case 'fullscreen':
            if(typeof(opt.template)=='undefined')return this;
            if(typeof(opt.RECORD)=='undefined')return this;
            opt.element='facebox';
            this._fullscreen(opt);
          break;

        /**
         * it is possible just to load new data in plugged elements, without initialization...
         */
        case 'redraw':
            if(typeof(opt.RECORD)=='undefined')return this;
            if(typeof(opt.element)=='undefined')opt.element = this.element;
            if(typeof(opt.element)=='string'){
              opt.element = $('#'+opt.element);
            };
            if(typeof(opt.ontime)!='undefined'){
              setTimeout(function(){
                that.redraw(opt.element.id,opt.RECORD);
              },opt.ontime);
            } else {
              this.redraw(opt.element.id,opt.RECORD);
            };
          break;

        /**
         * it is possible to HIDE elements...
         */
        case 'hide':
            if(typeof(opt.element)=='undefined')opt.element = this.element;
            if(typeof(opt.element)=='string'){
              opt.element = '#'+opt.element;
            };
            if(typeof(opt.ontime)!='undefined'){
              setTimeout(function(){
                $(opt.element).css('display','none');
                $(opt.element).addClass('waxed-hidden');
              },opt.ontime);
            } else {
              //$(opt.element).css('display','none');
              var hidden = $(opt.element).is(":hidden");
              //console.log('HIDDEN?', hidden);
              if (!hidden) {
                $(opt.element).addClass('waxed-hidden');
                $(opt.element).fadeOut(250);
              }
            };
          break;

        /**
         * it is possible to SHOW elements...
         */
        case 'show':
            if(typeof(opt.element)=='undefined')opt.element = this.element;
            if(typeof(opt.element)=='string'){
              opt.element = '#'+opt.element;
            };
            if(typeof(opt.ontime)!='undefined'){
              setTimeout(function(){
                $(opt.element).css('display','block');
                $(opt.element).removeClass('waxed-hidden');
              },opt.ontime);
            } else {
              //$(opt.element).css('display','block');
              var hidden = $(opt.element).is(":hidden");
              //console.log('HIDDEN?', hidden);
              if (hidden) {
                $(opt.element).fadeIn(250);
                $(opt.element).removeClass('waxed-hidden');
              }
            };
          break;

        /**
         * it is possible to INVALIDATE template...
         * For example, if user submited wrong data.
         * With this functionality,
         * its not needed to do duplicated and unsafe
         * browser-side validation anymore.
         */
        case 'invalidate':
            if((typeof(opt.RECORD)=='undefined')){
              return this;
            };
            //if(typeof(opt.element)=='undefined')opt.element = this.element;
            setTimeout(function(){
              that.invalidateTemplate(that.element.id,opt.RECORD);
              //that.invalidateTemplate('facebox',opt.RECORD);
            }, 500);
          break;

        /**
         * it is possible to programatically write into console...
         */
        case 'log':
            console.log(opt);
          break;

        /**
         * it is possible to ASSIGN templates,
         * load them directly through command,
         * instead of extra call to template source url...
         */
        case 'assign':
            if(typeof(opt.templates)=='undefined')return this;
            for(var x in opt.templates) {
              this.templates[x] = opt.templates[x];
            };
          break;

        /**
         * ckeditor callback...
         */
        case 'ckeditor/file/callback':
          //console.log(opt);
          window.opener.CKEDITOR.tools.callFunction(Number(opt.callback), opt.url);
          window.close();
        break;

        /**
         * iframe callback...
         */
        case 'iframe/callback':
          //$('input[]', window.parent.document).val();
          $('#'+opt.id, window.parent.document)[pluginName]({

          });
        break;

        /**
         * it is possible to define keystrokes, which will request server...
         * (This command should be extended!)
         */
        case 'keydown/load':
            if(typeof(opt.url)=='undefined')return this;
            if(typeof(opt.key)=='undefined')return this;
            if(typeof(opt.data)=='undefined')opt.data={action:'load'};
            $(document).bind('keydown', opt.key, function(){
              that.sendData(opt.data, opt.url);
            });
          break;

        /**
         * tell browser to request server either now or after some time...
         * (This command is default, if "action" is not set!)
         */
        case 'loadState':
        case 'load':
            if(typeof(opt.url)=='undefined')return this;
            if(typeof(opt.data)=='undefined')opt.data={};
            if (opt.action=='loadState') {
              if (window.location.hash) {
                opt.data.action = window.location.hash.substring(1);
              };
            };
            if (
              (typeof(opt.data.action) == 'undefined')
              ||
              (opt.data.action == '')
            ) {
              opt.data.action = 'load';
            };
            if(typeof(opt.ontime)!='undefined'){
              setTimeout(function(){
                that.sendData(opt.data, opt.url);
              },opt.ontime);
            }else{
              this.sendData(opt.data, opt.url);
            };
          break;

        /**
         * tell browser to request server after some time...
         * (Backward compatibility)
         */
        case 'ontime/load':
            if(typeof(opt.url)=='undefined')return this;
            if(typeof(opt.time)=='undefined')return this;
            if(typeof(opt.data)=='undefined')opt.data = {action:'load'};
            setTimeout(function(){
              that.sendData(opt.data, opt.url);
            }, opt.time);
          break;

        /**
         * it is possible to install some new behaviors...
         */
        case 'behave':
            if(typeof(opt.actions)=='undefined')return this;
            for(var x in opt.actions){
              this.setBehavior(x, opt.actions[x]);
            };
            //console.log('BEHAVE!');
          break;

        /**
         * to set hash in url...
         */
        case 'hashState':
            if(typeof(opt.state)=='undefined')return this;
            this.hashState(opt.state);
          break;

        /**
         * push state...
         * (This command could be extended!)
         */
        case 'pushState':
            if(typeof(opt.url)=='undefined')return this;
            if(typeof(opt.title)=='undefined')return this;
            window.history.pushState("object or string", opt.title, opt.url);
          break;

        /**
         * it is possible to SCROLL programatically on some X:Y point.
         */
        case 'scrollTo':
            if(typeof(opt.name)=='string'){
              var anch = $('[name='+opt.name+']');
              //console.log(anch);
              if (anch.length > 0) {
                opt.y = Math.floor($(anch[0]).offset().top);
                if (typeof opt.offset == 'number') {
                  opt.y = opt.y + opt.offset;
                };
                //console.log(opt);
              };
            };
            if(typeof(opt.y)=='undefined')return this;
            if(typeof(opt.x)=='undefined')opt.x=0;
            var speed='fast';
            if(typeof(opt.speed)!='undefined')speed=opt.speed;
            //console.log('SCROLLL');
            //window.scrollTo(opt.x, opt.y);
            $('html,body').animate({scrollLeft: opt.x, scrollTop: opt.y}, speed);
          break;

        case 'scrollTop':
            var speed='fast';
            if(typeof(opt.speed)!='undefined')speed=opt.speed;
            $('html,body').animate({scrollLeft: 0, scrollTop: 0}, speed);

            //window.scrollTo(0, 0);
            //console.log('SCROLL TOP');
          break;

        /**
         * we can address command also into named element's plugin...
         */
        case 'command':
            if(typeof(opt.ident)=='undefined')return this;
            //console.log(this.elements);
            if(typeof(this.elements[opt.ident])=='undefined')return this;
            if((typeof(opt.RECORD)!='undefined')&&(typeof(opt.RECORD.command)!='undefined')&&(typeof(opt.RECORD.value)!='undefined')){
              $(this.elements[opt.ident].o)[this.elements[opt.ident].t](opt.RECORD.command, opt.RECORD.value);
            } else {
              $(this.elements[opt.ident].o)[this.elements[opt.ident].t](opt.RECORD);
            };
          break;

        /**
         * it is possible to sent browser to other URL...
         */
        case 'gourl':
            if(typeof(opt.url)=='undefined')return this;
            if((typeof(opt.hard)=='boolean')&&(opt.hard)) {
              document.location.replace(opt.url);
            } else {
              document.location.href=opt.url;
            }
          break;

        /**
         * it is possible to RELOAD page in browser...
         */
        case 'reload':
            document.location.reload();
          break;

        /**
         * it is possible to clear space...
         */
        case 'clear':
            if(typeof(opt.element)=='undefined')opt.element = this.element;
            this._clear(opt);
          break;

        /**
         * it is possible to open new browser window...
         */
        case 'window/open':
            window.open(opt.url);
          break;

        /**
         * if there was added some plugins after initialization,
         * we can load them now...
         */
        case 'loadPlugins':
            this.loadPlugins();
          break;

        case 'func':
            opt.func();
          break;
        /**
         * it is possible to PLUG, load new plugins from server on demand...
         */
        case 'plug':
          //console.log('PLUG');
          if (typeof(opt.callback)=='function') {
            appendfunc(opt.callback);
          };
          var base = '';
          if (typeof(opt.base)=='string') {
            base = opt.base;
          };
          if (typeof(opt.data.js)!='undefined') {
            if (opt.data.js.length > 1) {
              this.queuePercent.reset();
            };
            var oo = [{action:'loadPlugins'}];
            this._queue = oo.concat(this._queue);
            var oo = [];
            for (var i = 0; i < opt.data.js.length; i++) {
              if (_loaded.js.indexOf(opt.data.js[i]) > -1) {
                continue;
              };
              if (i > 0) {
                ooo = {
                  action:'plug',
                  base:base,
                  data:{
                    js:[]
                  }};
                ooo.data.js.push(opt.data.js[i]);
                //console.log(ooo);
                oo.push(ooo);
                if (i == opt.data.js.length - 1) {
                  this._queue = oo.concat(this._queue);
                }
                continue;
              };
              var qp = this.queuePercent.get();
              $('.pluginfo').html(qp + '%');
              $('.plug-progress').trigger(pluginName+'-progress', qp);
              _loaded.js.push(opt.data.js[i]);
              var js = opt.data.js[i];
              $.ajax({
                url: base + opt.data.js[i],
                dataType: "script",
                cache: false,
                complete: [function(xhr) {
                  $(that.element).trigger(pluginName+'-script-loaded', xhr);
                }, that._reg()],
              })
              .done(function(x, y, z) {
                //that.loadPlugins();
                //console.log('plug', y, z);
              })
              .fail(function(x) {
                //console.log('plug failed', x);
              });
            };
          };
          if (typeof(opt.data.css)!='undefined'){
            for (var i=0;i<opt.data.css.length;i++){
              if (_loaded.css.indexOf(opt.data.css[i]) > -1) {
                continue;
              };
              _loaded.css.push(opt.data.css[i]);
              if (typeof(document.createStyleSheet)=='function') {
                document.createStyleSheet(opt.data.css[i]);
              } else {
                $('head')
                .append( $('<link rel="stylesheet" type="text/css" />')
                .attr('href', base + opt.data.css[i]) );

              }
            };
          };
          break;

        /**
         * this is case to dispatch multiple commands packed in one...
         */
        case 'multi':
            if(typeof(opt.actions)=='undefined')return this;
            this._queue = opt.actions.concat(this._queue);
            this.trigger();
            return this;
          break;

        case 'loadTemplate':
            var s = opt.html;
            Template._i(opt.name, s);
          break;

        case 'loadTemplateBlock':
            var s = $('#'+opt.tpl).html();
            Template._i(opt.tpl, '{{=%% %%=}}' + s);
            $('#'+opt.tpl).remove();
          break;
        case 'setBatch':
          this._running_batch = opt.batch;
          break;
        case 'stopBatch':
          this._running_batch = false;
          break;
        default:
            for (var x in this.controllers) {
              this.controllers[x].getInstance(this).trigger(opt);
            };
          break;
      }
      this._done();
      return this;
    },
    this.getme = function() {
      return that;
    },
    this.api = function() {
      return {
        'trigger':function(opt) {
          return that.trigger(opt);
        },
        'scrollTo':function(opt) {
          opt.action='scrollTo';
          return that.trigger(opt);
        },
        'sendData':function(data, url, obj) {
          return that.sendData(data, url, obj);
        },
        'pollData':function(data, url, obj) {
          return that.pollData(data, url, obj);
        },
        'findPluginInstances': function(pluginName, element) {
          return that.findPluginInstances(pluginName, element);
        },
        'find': function(pluginName, dataName) {
          var a = that.findPluginInstances(pluginName);
          for (var i = 0; i<a.length; i++) {
            if (a[i].$.data('name') == dataName) return a[i];
          }
          return;
        },
        'getPlugins': function() {
          return that.plugins;
        },
        'getRoute': function(name, resource) {
          return that._routes[name]+resource;
        },
        'dump': function() {
          console.log(that.behaviors);
        }
      };
    },

/*

    this.api = function() {
      return function() {
        //console.log('MAKING API');
        var a = {
          'trigger':function(opt) {
            return that.trigger(opt);
          },
          'sendData':function(data, url, obj) {
            return that.sendData(data, url, obj);
          },
          'qapi':function() {
            console.log('QAPI!', that.qapi());

            that.qapi().title({
              'title':'???'

            });



          }
        };
        var b = [
          'dialog', 'display', 'inspire', 'show', 'hide', 'scrollTo', 'scrollTop',
          'hashState', 'pushState', 'load', 'reload', 'clear', 'plug', 'dialogClose',
          'title', 'gourl', 'invalidate'
        ];
        for (var i = 0; i < b.length; i++) {
          var d = function() {
            var c = b[i];
            a[c] = function(opt) {
              opt.action = c;
              return that.trigger(opt);
            }
          }();

        };

        return a;
      };
    }(),


*/

    this.init();

  }

  if (typeof document.jammin == 'undefined')document.jammin = {};

  $[pluginName] = function (_name, _search, Instance, _api) {
    //console.log('$.'+pluginName, _search);
    document.jammin[pluginName+'/'+_name] = {
      search:_search,
      getInstance:function(plug, elem, data) {
        //var data = $(elem).data();
        //console.log('PLUGIN', _name);
        //console.log('DATA', data);

        if(!data['plugin_'+pluginName+'/'+_name]){
          $(elem).trigger(pluginName+'-plugin-instance-create', _name);
          plug.plugExtend(Instance, _api, _name);
          var o = new Instance(plug,elem,data)._api_();
          $.data(elem,'plugin_'+pluginName+'/'+_name, o);
          return o;
        }else{
          return data['plugin_'+pluginName+'/'+_name];
        }
      }
    };
    if (_search.charAt(0) == '^') {
      document.jammin[pluginName+'/'+_name]['root'] = 'body';
      document.jammin[pluginName+'/'+_name]['search'] = _search.substr(1);
    };
    //console.log('jammin:', document.jammin);
  };


  $.fn[pluginName] = function ( options ) {
      return this.each(function () {
          if (!$.data(this, 'plugin_' + pluginName)) {
              $(this).trigger(pluginName+'-instance-create', this.id);
              var o = new Plugin(this, options);
              //console.log(o);
              $.data(this, 'plugin_' + pluginName, o.api());
              return o.api();
          } else {
              var o = $.data(this, 'plugin_' + pluginName);
              o.trigger( options );
              return o;
              //return o.api();
          };
      });
  }

})( jQuery, window, document );
