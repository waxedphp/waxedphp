/******************************************************************************* CACHE */
  var LocalCache = function(namespace) {
    var _cacheProto = LocalCache.prototype;
    var _cache = this;
    this.namespace = ('undefined' == typeof namespace) ? null : namespace;
    this.expirationSuffix = '_expire';
    this._storages = ['storage1', 'storage2'];

    this.undefinedKey = '329a0f3c-5376-48c7-bf04-0e4d9cd100cb' + Date.now();
    this.undefinedValue = null;

    this._hashFnv32a = function(str, asString, seed) {
      /*jshint bitwise:false */
      var i, l,
          hval = (seed === undefined) ? 0x811c9dc5 : seed;

      for (i = 0, l = str.length; i < l; i++) {
          hval ^= str.charCodeAt(i);
          hval += (hval << 1) + (hval << 4) + (hval << 7) + (hval << 8) + (hval << 24);
      }
      if( asString ){
          // Convert to 8 digit hex string
          return ("0000000" + (hval >>> 0).toString(16)).substr(-8);
      }
      return hval >>> 0;
    };

    this.hash = function(string) {
      return _cache._hashFnv32a(string);
    };

    this.disabled = function() {
      return this._disabled;
    };

    this._clearExpiredItems = function() {
      if(_cache._disabled) return false;
      var now = Date.now();
      if((_cacheProto._expirations.lastExpired + 1000) > now) {
        return false; // too soon
      };
      _cacheProto._expirations.lastExpired = now;
      var cnt = 0;
      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        var keys = _cache[storage].getAllKeys('^(?!.*' + _cache.expirationSuffix + '$)');
        for (var i = 0; i < keys.length; i++) {
          var key = keys[i];
          var expiration = Number(_cache[storage].getItem(key + _cache.expirationSuffix));
          if ((expiration) && (expiration < now)) {
            //_cache[storage].removeItem(key.substr(0, key.length - _cache.expirationSuffix.length));
            _cache[storage].removeItem(key);
            _cache[storage].removeItem(key + _cache.expirationSuffix);
            cnt++;
          }
        }
      };
      //console.log('EXPIRED> ', cnt);
      return cnt;
    };

    this._initExpirations = function() {
      _cacheProto._expirations = {
        lastExpired: 0
      };
    };

    this._isOutOfSpace = function(e) {
        if (e && e.name === 'QUOTA_EXCEEDED_ERR'
          || e.name === 'NS_ERROR_DOM_QUOTA_REACHED'
          || e.name === 'QuotaExceededError') {
            return true;
        }
        return false;
    }

    this._namespaceKey = function(key, ns) {
        ns = ('undefined' == typeof ns) ? null : ns;
        var name = (ns !== null ? ns : _cache.namespace);
        if (null !== name) {
            name += '.';
        }
        return name + key;
    };

    this._expirationKey = function(key, ns) {
        ns = ('undefined' == typeof ns) ? null : ns;
        var name = _cache._namespaceKey(key, ns);
        return name + _cache.expirationSuffix;
    };

    this._set = function(namespaceKey, expirationKey, jsonvalue, expiration, level) {
      if (level >= _cache._storages.length) {
        return false;
      };
      var storage = _cache._storages[level];
      if (!_cache[storage].disabled()) {
        try {
          var result = _cache[storage].setItem(namespaceKey, jsonvalue);
          _cache[storage].setItem(expirationKey, expiration);
          //_cache._setExpiration(expirationKey, expiration, storage);
          return result;
        } catch(e) {
          if (_cache._isOutOfSpace(e)) {
            //console.log('out of space');
            _cache._clearExpiredItems();
            // no need to try immediatelly set here again, it will last a while,
            // go to next storage.
          };
          _cache[storage].removeItem(namespaceKey);
          _cache[storage].removeItem(expirationKey);
        }
      };
      level++;
      if (level >= _cache._storages.length) {
        // finally:
        //_cache._removeExpiration(expirationKey, expiration, storage);
        return false;
      };
      return _cache._set(namespaceKey, expirationKey, jsonvalue, expiration, level);
    };

    this.set = function(key, value, expiration, ns) {
      if(_cache._disabled) return false;
      var ns = ('undefined' == typeof ns) ? null : ns;
      var expiration = (expiration) ? Date.now() + expiration : null;
      var jsonvalue = window.JSON.stringify(value);
      return this._set(_cache._namespaceKey(key, ns), _cache._expirationKey(key, ns), jsonvalue, expiration, 0);
    };

    this._get = function(namespaceKey) {
      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        if ((!_cache[storage].disabled()) && (_cache[storage].hasKey(namespaceKey))) {
          return _cache[storage].getItem(namespaceKey);
        };
      };
      return _cache.undefinedValue;
    };

    this.get = function(key, ns) {
      if(_cache._disabled) return _cache.undefinedValue;
      var ns = ('undefined' == typeof ns) ? null : ns;
      var namespaceKey = _cache._namespaceKey(key, ns);
      var expirationKey = _cache._expirationKey(key, ns);
      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        if ((!_cache[storage].disabled())
          && (_cache[storage].hasKey(namespaceKey))
          && (_cache[storage].hasKey(expirationKey))) {
          var expiration = Number(_cache[storage].getItem(expirationKey));
          var value = _cache[storage].getItem(namespaceKey);
          if (expiration && expiration < Date.now()) {
            var res = _cache[storage].removeItem(namespaceKey);
            res = _cache[storage].removeItem(expirationKey) && res;
            //_cache._removeExpiration(expirationKey);
            return _cache.undefinedValue;
          };
          try {
            value = window.JSON.parse(value);
          } catch(e) {
            //console.log(e);
          }
          return value;
        };
      }
      return _cache.undefinedValue;
    };

    this.del = function(key, ns) {
      if(_cache._disabled) return false;
      ns = ('undefined' == typeof ns) ? null : ns;
      var namespaceKey = _cache._namespaceKey(key, ns);
      var expirationKey = _cache._expirationKey(key, ns);
      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        if ((!_cache[storage].disabled()) && (_cache[storage].hasKey(namespaceKey))) {
          var res = _cache[storage].removeItem(namespaceKey);
          res = _cache[storage].removeItem(expirationKey) && res;
          //_cache._removeExpiration(expirationKey);
          return res;
         }
      };
      return false;
    };

    this.clear = function() {
      if(_cache._disabled) return false;
      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        if (!_cache[storage].disabled()) {
          _cache[storage].clear();
        }
      };
    };

    this.getExpirations = function() {
      if(_cache._disabled) return [];
      return this._getExpirations();
    };

    this.getAllKeys = function(regex) {
      if(_cache._disabled) return [];
      var keys = [];
      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        keys = keys.concat(_cache[storage].getAllKeys(regex));
      };
      return keys;
    };

    this.getAll = function(regex) {
      if(_cache._disabled) return {};
      var keys = _cache.getAllKeys(regex);
      var data = {};
      for (var i = 0; i < keys.length; i++) {
        var key = keys[i];
        var val = _cache._get(key);
        try {
          data[key] = window.JSON.parse(val);
        } catch(e) {
          data[key] = val;
        }
      };
      return data;
    };

    /*** interface to LocalStorage: ***/
    this._init_storage1 = function() {
        var _data = null;
        _cacheProto.storage1 = new function() {
          var that = this;
          this.disabled = function() {
            if (!_data) return true;
            return false;
          };
          this.hasKey = function(key) {
            if (this.disabled()) return false;
            if (_data.hasOwnProperty(key)) {
              return true;
            };
            return false;
          };
          this.setItem = function(key, value) {
            if (this.disabled()) return false;
            _data.setItem(key, value);
          };
          this.getItem = function(key) {
            if (this.disabled()) return false;
            return _data.getItem(key);
          };
          this.removeItem = function(key) {
            if (this.disabled()) return false;
            return _data.removeItem(key);
          };
          this.clear = function() {
            if (this.disabled()) return false;
            return _data.clear();
          };
          this.getAllKeys = function(regex) {
            if (this.disabled()) return [];
            var regex = ('undefined' == typeof regex) ? null : new RegExp(regex);
            var keys = [];
            for (var key in _data) {
              if (!_data.hasOwnProperty(key)) {
                continue;
              }
              if (!regex) {
                keys.push(key);
              } else {
                if (regex.test(key)) {
                  keys.push(key);
                };
              }
            };
            return keys;
          };
          this._init = function() {
            try {
              _data = window.localStorage;
              if (typeof(_data) == 'undefined') {
                throw new Error('Storage is undefined');
              };
            } catch(err) {
              //console.log('LOCAL> ', err.name, err.message);
              _data = null;
            }
          }();
        };
    };
    /*** interface to simple object storage: ***/
    this._init_storage2 = function() {
        var _data = {};
        _cacheProto.storage2 = new function() {
          var that = this;
          this.disabled = function() {
            return false;
          };
          this.hasKey = function(key) {
            if (this.disabled()) return false;
            if (_data.hasOwnProperty(key)) {
              return true;
            };
            return false;
          };
          this.setItem = function(key, value) {
            if (this.disabled()) return false;
            _data[key] = value;
          };
          this.getItem = function(key) {
            if (this.disabled()) return false;
            if(_data.hasOwnProperty(key)){
              return _data[key];
            } else {
              return _cache.undefinedValue;
            }
          };
          this.removeItem = function(key) {
            if (this.disabled()) return false;
            if(_data.hasOwnProperty(key)){
              delete _data[key];
            };
          };
          this.clear = function() {
            if (this.disabled()) return false;
            _data = {};
          };
          this.getAllKeys = function(regex) {
            if (this.disabled()) return [];
            var regex = ('undefined' == typeof regex) ? null : new RegExp(regex);
            var keys = [];
            for (var key in _data) {
              if (!_data.hasOwnProperty(key)) {
                continue;
              }
              if (!regex) {
                keys.push(key);
              } else {
                if (regex.test(key)) {
                  keys.push(key);
                };
              }
            };
            return keys;
          };
        };
    };

    this._init = function() {

      if (typeof _cacheProto._disabled == 'undefined') {
        try {
          if (null == window.JSON) {
            throw 'No JSON support in the browser';
          };
        } catch(e) {
          _cacheProto._disabled = true;
          return false;
        }
        _cacheProto._disabled = false;
      }
      if (_cacheProto._disabled) return false;

      for (var j = 0; j < _cache._storages.length; j++) {
        var storage = _cache._storages[j];
        if (typeof(_cacheProto[storage]) == 'undefined') {
          var f = '_init_' + storage;
          _cache[f]();
        };
      };

      if (typeof(_cacheProto._expirations) == 'undefined') {
        _cache._initExpirations();
      };

      if (typeof(_cacheProto._tested) == 'undefined') {
        var test = false;
        var testKey = _cache.undefinedKey;
        var testValue = _cache.undefinedKey;
        try {
          _cache.set(testKey, testValue);
          if(_cache.get(testKey) === testValue){
              _cache.del(testKey);
              if(_cache.get(testKey) !== testValue){
                  _cache.undefinedValue = _cache.get(testKey);
                  test = true;
              } else {
                throw 'Deletion failed.';
              }
          } else {
            throw 'Setting failed.';
          }
        } catch(e) {
          //console.log(e);
        }

        if(!test){
          //console.log('test failed');
          _cacheProto._disabled = true;
        }
        _cacheProto._tested = true;
      };

      if (_cache.get('user', '_LocalCache') != window.userString) {
          _cache.clear();
          _cache.set('user', window.userString, null, '_LocalCache');
      } else {
          _cache._clearExpiredItems();
      }

    }();

  };
