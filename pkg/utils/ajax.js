/******************************************************************************* AJAX */
var Ajax = function(){
    var _ajax = this;
    if('function' !== typeof LocalCache
      || 'function' !== typeof $
      ) {
        throw 'Ajax: Requiring LocalCache and jQuery loaded.';
    };
    this.cache = new LocalCache('Ajax.class');
    this.jquery = $;
    this._messageService = null;

    // one fine example of stacked functions:
    this._do_the_debug = function(settings, stacks) {
      stacks.success.push(function() {
        console.log('SUCCESS ', arguments);
      });
      stacks.error.push(function() {
        console.log('ERROR ', arguments);
      });
      stacks.complete.push(function() {
        console.log('COMPLETE ', arguments);
      });
    };

    this._has_that_cached = function(settings, stacks) {
      if (!((typeof settings.expiration === 'number') && (settings.expiration > 0))) {
        return false;
      };
      var expiration = settings.expiration;
      var arbitrary = JSON.stringify(settings.data) + '>' + settings.url;
      var cache_key = _ajax.cache.hash(arbitrary, true);
      var cache_data = _ajax.cache.get(cache_key);
      if (cache_data) {
        if (settings.hasOwnProperty('success')) {
          for (var i in settings.success) {
            settings.success[i](cache_data);
          };
        };
        if (settings.hasOwnProperty('complete')) {
          for (var i in settings.complete) {
            settings.complete[i](null, 'success');
          };
        };
        return true;
      } else {
        stacks.success.push(function(data, textStatus, jqXHR) {
          if ((typeof data === 'object') && (!data.hasOwnProperty('error'))) {
            _ajax.cache.set(cache_key, data, expiration);
          };
        });
      };
      return false;
    };

    this._message = function(s) {
      if (!this._messageService) return;
      try{
        this._messageService.notify({
          message:s,
          type:'danger'
        });
      } catch(e) {
        //console.log(e);
      };
    };

    this._dispatch_response = function(data) {
      //console.log(data);
      var response = data.response;
      var status = data.status;
      // rework this evil! now for backward compatibility:

      if (response.hasOwnProperty('error') && (status == 200)) {

        if (response.hasOwnProperty('action') && (response.action === 'refresh')) {

          var href = window.location.href;
          if(href.indexOf('#') >= 0){
            href = href.substring(0, href.indexOf('#'));
          }
          window.location.href = href;

        } else if(response.hasOwnProperty('message')){
          _ajax._message(response.message);
        }

      } else if ((response.hasOwnProperty('message')) && (status != 200)) {
        _ajax._message(response.message);
      }

    };

    this._prepare_complete_handling = function(settings, stacks) {
      stacks.complete.push(function(jqXHR, textStatus) {
        if (!('undefined' !== typeof jqXHR && 'undefined' !== typeof jqXHR.status)) {
          return false;
        };
        if (jqXHR.status == 401) {
          // beware of loop ;-)
          window.location.href = window.location.href;
          return false;
        };
        var response = {};
        if (typeof jqXHR.responseJSON != 'undefined') {
          response = jqXHR.responseJSON;
        } else if ((typeof jqXHR.responseText != 'undefined') && (jqXHR.responseText)) {
          try{
            response = JSON.parse(jqXHR.responseText);
          } catch(e) {
            // console.log('this is not JSON response');
          };
        };

        _ajax._dispatch_response({
          'status': jqXHR.status,
          'statusText': jqXHR.statusText,
          'jquerySaid': textStatus,
          'response': response
        });

      });
    };

    // We want to have arrays here, to deal with the same type:
    this._unify_custom_callbacks = function(settings, stacks) {
      for (var prop in stacks) {
        if (typeof settings[prop] === 'object') {
          settings[prop] = settings[prop].filter(function(a) {return (typeof a === 'function');});
        } else if (typeof settings[prop] === 'function') {
          settings[prop] = [settings[prop]];
        } else {
          settings[prop] = [];
        }
      };
    };

    // Merge ajax callbacks with custom ones:
    this._finally_merge_callbacks = function(settings, stacks) {
      for (var prop in stacks) {
        (function(prop){
          stacks[prop] = stacks[prop].concat(settings[prop]);
          if (stacks[prop].length > 0) {
            // as of jQuery 1.5 the complete/error/success setting can accept an array of functions:
            settings[prop] = stacks[prop];
          };
        })(prop);
      };
    };

    this.call = function(settings){
      if('object' !== typeof settings){
        throw 'Settings are not an object';
      }
      if(!settings.hasOwnProperty('url')){
        throw 'Settings must contain a URL';
      }
      // If data are missing, unify:
      if (!settings.hasOwnProperty('data')) {
        settings.data = {};
      };

      var stacks = {success: [], error: [], complete: []};

      // unify callbacks to arrays:
      _ajax._unify_custom_callbacks(settings, stacks);

      if (settings.hasOwnProperty('debug') && settings.debug) {
        _ajax._do_the_debug(settings, stacks);
        delete settings.debug;
      };

      // If caching was turned on and is a positive number, maybe we return here.
      if (settings.hasOwnProperty('expiration')) {
        if (_ajax._has_that_cached(settings, stacks)) {
          return;
        };
        delete settings.expiration;
      };

      // Handling of errors, messages etc...
      if (true || (settings.hasOwnProperty('complete_handler'))) {
        _ajax._prepare_complete_handling(settings, stacks);
        delete settings.complete_handler;
      };

      // now push merged functions to ajax settings:
      _ajax._finally_merge_callbacks(settings, stacks);

      this.jquery.ajax(settings);
    };

};
var ajax = new Ajax();
