/*
  mark2.js
  MIT License
  (c) 2011 - 2014 Adam Mark
  (c) 2017 Andy Bezák
*/
(function(window) {
  var rAmp = /&/g,
    rLt = /</g,
    rGt = />/g,
    rApos = /\'/g,
    rQuot = /\"/g,
    hChars = /[&<>\"\']/,

    rIsWhitespace = /\S/,
    rQuot = /\"/g,
    rNewline = /\n/g,
    rCr = /\r/g,
    rSlash = /\\/g,
    rLineSep = /\u2028/,
    rParagraphSep = /\u2029/;
  var n = 0;
  var lastNamed = '';


  var Mark = {
    // Templates to include, by name. A template is a string.
    includes: {},

    // Global variables, by name. Global variables take precedence over context variables.
    globals: {},

    // Partials, as seen in Mustache, Handlebars, Hogan...
    partials: {},

    // The delimiter to use in pipe expressions, e.g. {{if color|like>red}}.
    delimiter: ">",

    dialect: 'mustache', // mark or mustache //

    // Collapse white space between HTML elements in the resulting string.
    compact: false,

    _dot: function(str, arr, idx) {
      if (typeof idx == 'number') {
        idx = '[' + idx + ']';
      } else {
        idx = '';
      }
      var astr;
      if (typeof str == 'string') {
        astr = str.split('.');
      } else {
        astr = str;
      }
      if (typeof arr == 'string') {
        arr = arr.split('.');
      };
      var a = astr.concat(arr).filter(function(b) {
        return (typeof b == 'string') && (b);
      });
      return a.join('.') + idx;
    },

    _isArray: function(obj) {
      return (Object.prototype.toString.call(obj) === '[object Array]');
    },

    _isScalar: function(obj) {
      return ((typeof obj != 'object') && (typeof obj != 'function'));
    },

    _escapeHTML: function(str) {
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(str));
      var s = div.innerHTML;
      //div.parentNode.removeChild(div);
      return s;
    },

    _coerceToString: function(val) {
      return String((val === null || val === undefined) ? '' : val);
    },

    _Escape: function(str) {
      str = this._coerceToString(str);
      return Mark._escapeHTML(str);
      if (hChars.test(str)) {
        return str
        .replace(rAmp, '&amp;')
        .replace(rLt, '&lt;')
        .replace(rGt, '&gt;')
        .replace(rApos, '&#39;')
        .replace(rQuot, '&quot;')
      } else {
        return str;
      }

    },

    _esc: function(s) {
      return s.replace(rSlash, '\\\\')
        .replace(rQuot, '\\\"')
        .replace(rNewline, '\\n')
        .replace(rCr, '\\r')
        .replace(rLineSep, '\\u2028')
        .replace(rParagraphSep, '\\u2029');
    },

    _escapeSource: function(str) {
      str = str.replace(/\s+/g, " ").replace(/>\s+</g, "><");
      return '\'' + str + '\'';
    },



    // Shallow-copy an object.
    _copy: function(a, b) {
      b = b || [];
      for (var i in a) {
        b[i] = a[i];
      }
      return b;
    },

    _same: function(a) {
      return a;
    },

    _extend: function(a, b) {
      for (var x in b) {
        a[x] = b[x];
      }
      return a;
    },

    _keys: function(a) {
      var b = [];
      for (var x in a) {
        b.push(x);
      }
      return b;
    },

    _clone: function(obj) {
      var copy;
      // Handle the 3 simple types, and null or undefined
      if (null == obj || "object" != typeof obj) return obj;
      // Handle Date
      if (obj instanceof Date) {
        copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
      }
      // Handle Array
      if (obj instanceof Array) {
        copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
          copy[i] = this._clone(obj[i]);
        }
        return copy;
      }
      // Handle Object
      if (obj instanceof Object) {
        copy = {};
        for (var attr in obj) {
          if (obj.hasOwnProperty(attr)) copy[attr] = this._clone(obj[attr]);
        }
        return copy;
      }
      throw new Error("Unable to copy obj! Its type isn't supported.");
    },

    // Get the value of a number or size of an array. This is a helper function for several pipes.
    _size: function(a) {
      //return a instanceof Array ? a.length : (a || 0);
      if ((a instanceof Array) || (typeof a == 'string')) {
        return a.length;
      }
      if ((typeof a == 'number')) {
        return a;
      }
      return 0;
    },

    // This object represents an iteration. It has an index and length.
    _iter: function(idx, size) {
      this.idx = idx;
      this.size = size;
      this.length = size;
      this.sign = "$";
      // Print the index if "#" or the count if "##".
      this.toString = function() {
        return this.idx + this.sign.length - 1;
      };
    },

    _get_pipe_func: function(expressions) {
      var reg = /^[a-z0-9\_]+$/i
      var parts, fn, i, script = 'function(val,io){';
      script += 'try{';
      for (i = 0; i < expressions.length; i++) {
        parts = expressions[i].split(this.delimiter);
        fn = parts.shift().trim();
        if (!reg.test(fn)) {
          //console.log('wrong helper function name!');
          script += 'return false}';
          return script;
        };
        //script += 'try{';
        script += 'val=MP(\'' + fn + '\').apply(null,[val]';
        if (parts.length > 0) {
          script += '.concat(' + JSON.stringify(parts) + ')';
        }
        script += '.concat({io:io}));';
        //script += '}catch(e){console.log(\'eee\', e);};';
        //script += '}catch(e){};';
      }
      script += '}catch(e){};';
      //script += 'console.log(\'' + fn + '\', \'VAL\', val);';
      script += 'return val}';
      return script;
    },

    _setPaths: function(length, oi) {
      var n = 0;
      //function numDigits(x) {
      //  return Math.max(Math.floor(Math.log10(Math.abs(x))), 0) + 1;
      //}
      //And an optimized version of the above (more efficient bitwise operations):
      var numDigits = function(x) {
        return (Math.log10((x ^ (x >> 31)) - (x >> 31)) | 0) + 1;
      }
      var L = numDigits(length);
      oi._autopath = function(chr) {
        n++;
        if (!chr) chr = 'f';
        return '' + chr + ('0000000000' + n).slice(-L) + '_';
      };
      oi._path = function(index) {
        return 'f' + ('0000000000' + index).slice(-L) + '_';
      };
    },



    _makeFlags: function(tag) {
      var f = 0;
      (tag.itery) ? f = f | 1: false;
      (tag.selfy) ? f = f | 2: false;
      (tag.testy) ? f = f | 4: false;
      (tag.dotty) ? f = f | 8: false;
      (tag.unsafe) ? f = f | 16: false;
      (tag.ignore) ? f = f | 32: false;
      (tag.partialy) ? f = f | 64: false;
      (tag.register) ? f = f | 128: false;
      (tag.negate) ? f = f | 256: false;
      return f;
    },

    _buildHelpers: function(helpers, oi) {
      var h = helpers.join('|');
      var helpersId = '';
      if (h) {
        if (typeof oi._compilation_helpers[h] == 'undefined') {
          oi._compilation_helpers[h] = {
            id: oi._autopath('h'),
            stack: helpers,
            _: false
          };
        };
        helpersId = oi._compilation_helpers[h].id;
      }
      return helpersId;
    },

    _buildTree: function(template, tags, tag, tree, oi) {

      if (tag.position == 'end') {
        return;
      }
      if (tag.command) {
        return;
      }
      if (tag.ignore) {
        tag.script = this._escapeSource(template.substring(tag.innerBegin, tag.innerEnd));
        tree[oi._path(tag.index)] = this._same(tag);
        return;
      }
      if ((!tag.children.length) && (!tag.testy) && (!tag.itery)) {
        if (tag.index === 0) {
          /* Nothing here to do, just remember the whole root. */
          tag.script = this._escapeSource(template);
          tree[oi._path(tag.index)] = this._same(tag);
        };
        return;
        tag.t = template.substring(tag.begin, tag.end);
        var tp = tag.parent;
        if (!tp) tp = 0;
        tag.datapath = this._dot(tags[tp].datapath, tag.variable);
        //' + this._path(tag.index) + '
        tag.script = 'r(0, io, ' + this._makeFlags(tag) + ', \'' + tag.variable + '\', \'' + this._buildHelpers(tag.helpers, oi) + '\')'
        tree[oi._path(tag.index)] = this._same(tag);
        return;
      }
      var outerHTML = '';
      var innerHTML = '';
      var begin = tag.innerBegin;
      var end = 0;
      var tp = tag.parent;
      if (!tp) tp = 0;
      tag.datapath = this._dot(tags[tp].datapath, tag.variable);
      var partialHelpersList = {};

      for (var i = 0; i < tag.children.length; i++) {

        if (tags[tag.children[i]].position == 'end') {
          continue;
        }

        end = tags[tag.children[i]].outerBegin;
        innerHTML += this._escapeSource(template.substring(begin, end));
        var childTag = tags[tag.children[i]];
        var childHelpersId = '';
        if (childTag.helpers.length > 0) {
          childHelpersId = this._buildHelpers(childTag.helpers, oi);
          if (childTag.register) {
            partialHelpersList[childHelpersId] = true;
          }
        };

        var tp = childTag.parent;
        if (!tp) tp = 0;
        childTag.datapath = this._dot(tags[tp].datapath, childTag.variable);
        if (childTag.testy) {
          var elseFuncID = false;
          if (typeof childTag.elsetag == 'object') {
            elseFuncID = oi._path(childTag.elsetag.index);
          };
          if (!childTag.elsy) {
            innerHTML += '+t(\'' + oi._path(childTag.index) + '\',io,' + this._makeFlags(childTag) + ',\'' + childTag.variable + '\',\'' + childHelpersId + '\',\'' + elseFuncID + '\')+';
          } else {
            innerHTML += '+\'\'+';
          }
        } else if (childTag.partialy) {
          innerHTML += '+i(\'' + childTag.variable + '\',io,' + this._makeFlags(childTag) + ',\'@current\',\'' + childHelpersId + '\')+';
        } else if (!childTag.itery) {
          innerHTML += '+r(\'' + oi._path(childTag.index) + '\',io,' + this._makeFlags(childTag) + ',\'' + childTag.variable + '\',\'' + childHelpersId + '\')+';
        } else {
          innerHTML += '+i(\'' + oi._path(childTag.index) + '\', io,' + this._makeFlags(childTag) + ',\'' + childTag.variable + '\',\'' + childHelpersId + '\')+';
        }


        begin = tags[tag.children[i]].outerEnd;
      };
      var end = tag.innerEnd;

      innerHTML += this._escapeSource(template.substring(begin, end));
      tag.script = innerHTML;
      if (tag.register) {
        //tree[this._path(tag.index)] = {script:'\'\''};
        if (typeof oi._compilation_partials[tag.register] == 'undefined') {
          oi._compilation_partials[tag.register] = {
            'entry': oi._path(tag.index),
            'stack': [],
            'helpers': {}
          };
        }
        oi._compilation_partials[tag.register]['stack'].push(oi._path(tag.index));
        this._extend(oi._compilation_partials[tag.register]['helpers'], partialHelpersList);
        //console.log('PARTIAL ADDED', this._compilation_partials);
      }
      tree[oi._path(tag.index)] = this._same(tag);
    },

    _findPair: function(tags, tag) {
      var cnt = 1;
      for (var i = tag.index + 1; i < tags.length; i++) {
        var otherTag = tags[i];

        if ((tag.token == otherTag.token) && (otherTag.position == 'begin')) {
          cnt++;
        };
        if ((tag.testy) && (otherTag.token == 'else') && (cnt == 1)) {
          tag.elsetag = otherTag;
          tags[i].paired = true;
        };
        if ((tag.token == otherTag.token) && (otherTag.position == 'end')) {
          cnt--;
          if (cnt == 0) {
            tag.paired = true;
            tag.closing = otherTag;
            tag.outerBegin = tag.begin;
            tag.innerBegin = tag.end;
            tag.outerEnd = otherTag.end;
            tag.innerEnd = otherTag.begin;
            tags[i].paired = true;
            if (tag.register) {
              tags[i].register = tag.register;
            };
            if ((tag.testy) && (tag.elsetag)) {
              var elseTag = tags[tag.elsetag.index];
              tag.outerEnd = elseTag.begin;
              tag.innerEnd = elseTag.begin;
              tag.closing = elseTag;
              elseTag.outerBegin = elseTag.begin;
              elseTag.innerBegin = elseTag.end;
              elseTag.outerEnd = otherTag.end;
              elseTag.innerEnd = otherTag.begin;
              elseTag.closing = otherTag;
              elseTag.testy = true;
              elseTag.elsy = true;
            };
            return;
          };
        };
      };
    },

    _findChildren: function(tags, tag) {
      if (!tag.closing) {
        return false;
      }
      var cnt = 1;
      var end = tag.closing.index;
      for (var i = tag.index + 1; i < end; i++) {
        tags[i].parent = tag.index;
        if (tag.register) {
          tags[i].register = tag.register;
        }
      };
    },

    _findMustaches: function(tpl) {
      var exp = '(\}?)\{\{(.+?)\}\}(\}?)';
      var regex = new RegExp(exp, 'g');
      var regControl = /^[a-z0-9\_\.\!\@\$\:\^]+$/i
      var match;
      var tags = [{
        index: 0,
        name: 'root',
        children: [],
        datapath: ''
      }];
      var index = 1;
      while (match = regex.exec(tpl)) {
        // The tag being evaluated, e.g. "{{hamster|dance}}".
        var tag = match[0];
        // Mustache specs, content for this tag will not be escaped.
        var unsafe = (tag.indexOf("{{{") === 0);
        // Does the tag close itself? e.g. "{{stuff/}}".
        var selfy = (tag.indexOf("/}}") > -1);
        // The expression to evaluate inside the tag, e.g. "hamster|dance".
        var prop = tag.substr((unsafe ? 3 : 2), tag.length - ((selfy ? 1 : 0) + (unsafe ? 6 : 4)));
        // Is the tag an "if" statement?
        var testy = prop.trim().replace(/^[\/\#]/, '').indexOf("if ") === 0;
        var register = prop.trim().replace(/^[\/\#]/, '').indexOf("!register ") === 0;
        var propSplit = prop.split("|");
        var variable = propSplit.shift().replace(/^[\/\#\^\>]/, '');
        var variableSplit = variable.trim().split(/\s/);
        var token = variable.replace(/^[\/\#\^]/, '');
        token = variableSplit[0];
        variable = variableSplit[variableSplit.length - 1];
        if ((testy) || (register)) {
          //token = variableSplit[0];

          if (register) {
            register = variable;
            //console.log('register', register);
          }
        };
        var ignore = (token == '!ignore');
        var command = (token == '!cmd');
        if (command) {
          //console.log(variableSplit);
          if ((typeof variableSplit[1] == 'string')&&(variableSplit.length > 2)) {
            if (variableSplit[1] == 'dialect') {
              this.dialect = variableSplit[2];
            }
          }
        };
        var negate = false;
        if (this.dialect == 'mark') {
          var itery = !selfy;
        } else {
          // Is the tag start of block?
          var itery = prop.trim().indexOf("#") === 0;
          if (prop.trim().indexOf("^") === 0) {
            itery = true;
            negate = true;
          }
        };

        //var bridge = [];
        var helpers = [].concat(propSplit);
        // Is this the partial?
        var partialy = prop.trim().indexOf(">") === 0;
        // Is the tag the current context tag?
        var dotty = (variable === '.');
        var error = 0;
        if (!regControl.test(variable)) {
          error = error|1;
        };
        if (!regControl.test(token)) {
          error = error|2;
        };
        if (error) {
          //console.log(variable, token);
          throw error + ':BAD!';
        };

        tags.push({
          index: index++,
          begin: match.index,
          end: regex.lastIndex,
          position: (match[0].indexOf("{{/") < 0) ? 'begin' : 'end',
          length: tag.length,
          control: (regex.lastIndex - match.index),
          tag: tag,
          child: "",
          children: [],
          dotty: dotty,
          selfy: selfy,
          unsafe: unsafe,
          error: error,
          testy: testy,
          itery: itery,
          partialy: partialy,
          negate: negate,
          token: token,
          variable: variable,
          helpers: helpers,
          datapath: '',
          ignore: ignore,
          command: command,
          register: register,
          prop: prop.replace(/^\s*if/, "").split("|").shift().trim(),
          _: false
        });
      }

      for (var i = 0; i < tags.length; i++) {
        var tag = tags[i];
        if (this.dialect == 'mark') {
          //console.log(tag);
        }
        if ((tag.testy) || (tag.itery)) {
          this._findPair(tags, tag);
        } else {
          if (!tag.paired) {
            tags[i].outerBegin = tag.begin;
            tags[i].outerEnd = tag.end;
          }
          if (tag.dotty) {
            tag.variable = '@current';
          }
        }
      }
      for (var i = 0; i < tags.length; i++) {
        var tag = tags[i];
        if ((tag.testy) || (tag.itery)) {
          this._findChildren(tags, tag);
        }
      }
      for (var i = 0; i < tags.length; i++) {
        var tag = tags[i];
        if (typeof tag.parent != 'undefined') {
          tags[tag.parent].children.push(tag.index);
        } else if (tag.index > 0) {
          tags[i].parent = 0;
          tags[0].children.push(tag.index);
        }
      }
      //console.log('MUSTACHES', tags);
      return tags;
    }

  };

  // Inject a template string with contextual data and return a new string.
  Mark.up = function(template, context, options, path) {
    context = context || {};
    options = options || {};
  };


  Mark.renders = {
    g: function(io) { // register partials
      if (typeof io.o['partials'] == 'object') {
        for (var name in io.o['partials']) {
          io.mark.partials[name] = {
            _: false
          };
          var partial = io.o.partials[name];
          for (var i = 0; i < partial.length; i++) {
            var fn = partial[i];
            if (typeof io.o[fn] == 'function') {
              io.mark.partials[name][fn] = io.o[fn];
              if (!io.mark.partials[name]._) {
                io.mark.partials[name]._ = fn;
              }
            }
          }
        }
      }
      //console.log('PARTIALS REGISTERED:', io.mark.partials);
    },
    x: function(path, io, flags) {
      var xtree = false;
      io._path_parts_ = [];
      if (typeof io.mark.globals[path] != 'undefined') {
        xtree = io.mark.globals[path];
      };

      if (!xtree) {
        xtree = io.tree;
        var a = path.split('.');
        if (a[0].match(/^\:/)) {
          a[0] = a[0].replace(/^\:/, '');
          xtree = io.input;
          io._path_ = '';
        }
        io._path_parts_ = a;
        for (var i = 0; i < a.length; i++) {
          var key = a[i];
          if (key == '@current') {


          } else if (key.match(/^(\@|\$|\!)/)) {

            switch (key) {
              case '$':
              case '@index':
                if (io.mark._isScalar(io._index_)) return io._index;
                return false;
                break;
              case '$$':
              case '@idx':
                if (io.mark._isScalar(io._index_)) return io._index_ + 1;
                return false;
                break;
              case '@length':
                if (io.mark._isArray(xtree)) {
                  return xtree.length;
                }
                if (io.mark._isScalar(io._length_)) return io._length_;
                return false;
                break;
              case '@path':
                if (io.mark._isScalar(io._path_)) return io._path_;
                return false;
                break;
              case '@time':
                var d = new Date;
                return d.getTime();
                break;
              case '!ignore':
                //console.log('IGNORE', xtree);
                return true;
                break;
            };

          } else {
            if (typeof xtree[key] == 'undefined') {
              //console.log('MISSED PATH', key);
              if (flags&256) {
                return xtree;
              };
              return false;
            }
            xtree = xtree[key];
          }
        };
      };
      if (flags&256) {
        if (xtree === false) {
          return true;
        }
        return false;
      };
      return xtree;
    },
    p: function(value, flags, io) {
      var s = '';
      if (flags & 16) { //unsafe
        s = (value);
      } else {
        s = io.mark._Escape(value);
      }
      return s;
    },
    h: function(helpersid, io, flags) {
      var helpersfunc = function(xtree){return xtree;};
      if ((flags & 128) && (helpersid) && (typeof io.mark.partials[io['_partial_']] != 'undefined')) {
        if (typeof io.mark.partials[io['_partial_']][helpersid] != 'undefined') {
          helpersfunc = io.mark.partials[io['_partial_']][helpersid](io);
        }
      } else if ((helpersid) && (typeof io.o[helpersid] != 'undefined')) {
        helpersfunc = io.o[helpersid](io); //mark._pipe(xtree, filters.split('|'), io);
      };
      return helpersfunc;
    },
    r: function(scriptid, io, flags, path, helpersid) {
      var _path_ = io['_path_'];
      var _partial_ = io['_partial_'];
      var origin = io.tree;
      var xtree = this.x(path, io, flags);
      if (!xtree) {
        io['_path_'] = _path_;
        io['_partial_'] = _partial_;
        return '';
      };
      var _path_parts_ = io._path_parts_;
      var s = '';
      var helpersfunc = this.h(helpersid, io, flags);
      io['_path_'] = io.mark._dot(_path_, _path_parts_, '');
      xtree = helpersfunc(xtree, io);

      if (io.mark._isScalar(xtree)) {
        s = this.p(xtree, flags, io);
      } else if ((io.mark._isArray(xtree)) && (flags & 8) && (key == '@current')) {
        s = this.p(xtree[io._index_], flags, io);
      } else {
        //console.log('WRONG', path, xtree, typeof xtree);
      };

      io['_path_'] = _path_;
      io['_partial_'] = _partial_;
      io.tree = origin;
      return s;
    },
    i: function(scriptid, io, flags, path, helpersid) {
      var _path_ = io['_path_'];
      var _partial_ = io['_partial_'];
      var origin = io.tree;
      var xtree = this.x(path, io, flags);
      if (!xtree) {
        return '';
      };
      var _path_parts_ = io._path_parts_;
      io['_path_'] = io.mark._dot(_path_, _path_parts_, '');
      var s = '';
      var script = false;
      var helpersfunc = this.h(helpersid, io, flags);
      if (flags & 64) { // requested partial
        //console.log(io.mark.partials, scriptid);
        if ((io['_partial_'] != scriptid) && (typeof io.mark.partials[scriptid] != 'undefined')) {
          io['_partial_'] = scriptid;
          var entryPoint = io.mark.partials[scriptid]._;
          script = io.mark.partials[scriptid][entryPoint];
        }
      } else if (flags & 128) { // inside partial
        if ((io['_partial_']) && (typeof io.mark.partials[io['_partial_']] != 'undefined')) {
          if (typeof io.mark.partials[io['_partial_']][scriptid] != 'undefined') {
            script = io.mark.partials[io['_partial_']][scriptid];
          }
        }
      } else {
        if (typeof io.o[scriptid] != 'undefined') {
          script = io.o[scriptid];
        }
      }
      xtree = helpersfunc(xtree);
      if (script) {
        if (io.mark._isArray(xtree)) {
          io['_length_'] = xtree.length;
          for (var i = 0; i < xtree.length; i++) {
            io['_index_'] = i;
            io['_path_'] = io.mark._dot(_path_, _path_parts_, i);
            io.tree = xtree[i];
            s += script(io);
            io.tree = xtree;
          }
          delete io['_index_'];
          delete io['_length_'];
        } else {
          io.tree = xtree;
          s = script(io);
          io.tree = xtree;
        }
      }
      io.tree = origin;
      io['_path_'] = _path_;
      io['_partial_'] = _partial_;
      return s;
    },
    t: function(scriptid, io, flags, path, helpersid, elseid) { // TESTY
      var _path_ = io['_path_'];
      var origin = io.tree;
      var xtree = this.x(path, io, flags);
      if (!xtree) {
        return '';
      };
      var _path_parts_ = io._path_parts_;
      //make test here
      var test = false;
      if ((helpersid) && (typeof io.o[helpersid] != 'undefined')) {
        test = io.o[helpersid](io)(xtree, io); //mark._pipe(xtree, filters.split('|'), io);
      };
      xtree = origin;
      io['_path_'] = _path_;
      var s = '';
      if (test === false) {
        if (elseid && (typeof io.o[elseid] != 'undefined')) {
          io.tree = xtree;
          s = io.o[elseid](io);
          io.tree = xtree;
        } else {
          io.tree = origin;
          io['_path_'] = _path_;
          return '';
        }
      } else if (typeof io.o[scriptid] != 'undefined') {
        io.tree = xtree;
        s = io.o[scriptid](io);
        io.tree = xtree;
      }
      io.tree = origin;
      io['_path_'] = _path_;
      return s;
    },
    e: function(io) {
      io.path = '';
      io.partial = false;
      io['_path_'] = '';
      io.tree = io.input;
      io.output = io.o[io.entry](io);
    }
  };

  Mark._buildScript = function(tree, oi) {
    var apos = '\'';
    var _script_ = '(function(io){' + "\n";
    _script_ += 'var o={' + "\n";
    var _named_ = '';
    for (var x in tree) {
      if (typeof tree[x].script == 'string') {
        _script_ += apos + x + apos + ':function (io){return ' + tree[x].script.replace(/(?:\r\n|\r|\n)/g, '') + "},\n"; //
      } else if (typeof tree[x].stack == 'string') {
        if (_named_) _named_ += ",\n";
        _named_ += apos + x + apos + ':' + tree[x].stack;
      }
    };
    _script_ += apos + 'partials' + apos + ':{' + _named_ + '}};' + "\n";
    _script_ += 'var M=io.mark;var MR=M.renders;var MP=M.piper;';
    _script_ += 'var t=function(){return MR.t.apply(MR,arguments)};';
    _script_ += 'var r=function(){return MR.r.apply(MR,arguments)};';
    _script_ += 'var i=function(){return MR.i.apply(MR,arguments)};';
    _script_ += 'function e(io){io.o=o;MR.g(io);io.entry=\'' + oi._path(0) + '\';MR.e(io);}' + ";\n";
    _script_ += 'e(io);})(arguments[0]);';

    //console.log(_script_);
    return _script_;

  }

  Mark.Twain = function(template, options, path) {
      options = options || {};
      var entryPoint = false;
      var oi = {};
      if (!path) {
        n++; // entry point
        entryPoint = true;
        path = '';
        template = template.replace(/\'/g, "\\\'");
      }

      // Match all tags like "{{...}}".
      //var re = /\{\{(.+?)\}\}(\}?)/g;
      //var tags = [];
      var tags = this._findMustaches(template);
      var tree = {};
      oi._compilation_helpers = {};
      oi._compilation_partials = {};
      this._setPaths(tags.length, oi);
      for (var i = 0; i < tags.length; i++) {
        this._buildTree(template, tags, tags[i], tree, oi);
      }
      //console.log('tree', tree);

      for (var x in oi._compilation_partials) {
        var pt = oi._compilation_partials[x];
        tree[x] = {
          stack: JSON.stringify(pt.stack.concat(this._keys(pt.helpers)))
        };
      }

      for (var x in oi._compilation_helpers) {
        var hp = oi._compilation_helpers[x];
        tree[hp.id] = {
          script: this._get_pipe_func(hp.stack),
          _: false
        };
      }
      delete oi._compilation_helpers;
      delete oi._compilation_partials;
      var _script_ = this._buildScript(tree, oi);
      //console.log(_script_);
      delete oi._path; delete oi._autopath;
      return _script_;
    },

    Mark.compile = function(template, options) {
      var _script_ = this.Twain(template, {}, options);
      //console.log(_script_);
      return _script_;
    };

  Mark.render = function(_script_, context) {
    var ff = new Function(_script_);
    var io = {
      input: context,
      output: '',
      mark: {
        renders: this.renders,
        piper: this.piper,
        globals: this.globals,
        partials: this.partials,
        _isScalar: this._isScalar,
        _isArray: this._isArray,
        _dot: this._dot,
        _Escape: this._Escape,
        _coerceToString: this._coerceToString
      }
    };
    ff(io);
    //console.log(io.output);
    return io.output;
  };

  Mark.registerHelper = function(name, helperFunction) {
    if (typeof arguments[0] == 'object') {
      for (var x in arguments[0]) {
        this.registerHelper(x, arguments[0][x]);
      }
      return;
    };
    Mark.pipes[name] = helperFunction;
  };

  Mark.unregisterHelper = function(name) {
    if (typeof arguments[0] == 'object') {
      for (var i = 0; i < arguments[0].length; i++) {
        this.unregisterHelper(arguments[0][i]);
      }
      return;
    };
    delete Mark.pipes[name];
  };

  Mark.registerGlobal = function(name, content) {
    if (typeof arguments[0] == 'object') {
      for (var x in arguments[0]) {
        this.registerGlobal(x, arguments[0][x]);
      }
      return;
    };
    Mark.globals[name] = content;
  };

  Mark.unregisterGlobal = function(name) {
    if (typeof arguments[0] == 'object') {
      for (var i = 0; i < arguments[0].length; i++) {
        this.unregisterGlobal(arguments[0][i]);
      }
      return;
    };
    delete Mark.globals[name];
  };

  Mark.registerPartial = function(name, content) {
    if (typeof arguments[0] == 'object') {
      for (var x in arguments[0]) {
        this.registerPartial(x, arguments[0][x]);
      }
      return;
    };
    Mark.partials[name] = content;
  };

  Mark.unregisterPartial = function(name) {
    if (typeof arguments[0] == 'object') {
      for (var i = 0; i < arguments[0].length; i++) {
        this.unregisterGlobal(arguments[0][i]);
      }
      return;
    };
    delete Mark.partials[name];
  };


  Mark.piper = function(name) {
    try {
      return Mark.pipes[name];
    }catch(e){
      //console.log('error', e);
    }
    if (typeof Mark.pipes[name] == 'function') {
      return Mark.pipes[name];
    };
    return function(x){return x;};
  };

  // Freebie pipes. See usage in README.md
  Mark.pipes = {
    empty: function(obj) {
      return !obj || (obj + "").trim().length === 0 ? obj : false;
    },
    notempty: function(obj) {
      return obj && (obj + "").trim().length ? obj : false;
    },
    blank: function(str, val) {
      //console.log('BLANK ???', str, val);
      var x = !!str || str === 0 ? str : val;
      //console.log(x);
      return x;
    },
    more: function(a, b) {
      return Mark._size(a) > b ? a : false;
    },
    less: function(a, b) {
      return Mark._size(a) < b ? a : false;
    },
    ormore: function(a, b) {
      return Mark._size(a) >= b ? a : false;
    },
    orless: function(a, b) {
      return Mark._size(a) <= b ? a : false;
    },
    between: function(a, b, c) {
      a = Mark._size(a);
      return a >= b && a <= c ? a : false;
    },
    equals: function(a, b) {
      return a == b ? a : false;
    },
    notequals: function(a, b) {
      return a != b ? a : false;
    },
    like: function(str, pattern) {
      return new RegExp(pattern, "i").test(str) ? str : false;
    },
    notlike: function(str, pattern) {
      return !Mark.pipes.like(str, pattern) ? str : false;
    },
    upcase: function(str) {
      return String(str).toUpperCase();
    },
    downcase: function(str) {
      return String(str).toLowerCase();
    },
    capcase: function(str) {
      return str.replace(/(?:^|\s)\S/g, function(a) {
        return a.toUpperCase();
      });
    },
    chop: function(str, n) {
      return str.length > n ? str.substr(0, n) + "..." : str;
    },
    tease: function(str, n) {
      var a = str.split(/\s+/);
      return a.slice(0, n).join(" ") + (a.length > n ? "..." : "");
    },
    trim: function(str) {
      return str.trim();
    },
    pack: function(str) {
      return str.trim().replace(/\s{2,}/g, " ");
    },
    round: function(num) {
      return Math.round(+num);
    },
    clean: function(str) {
      return String(str).replace(/<\/?[^>]+>/gi, "");
    },
    size: function(obj) {
      return obj.length;
    },
    length: function(obj) {
      return obj.length;
    },
    reverse: function(arr) {
      return [].concat(arr).reverse();
    },
    join: function(arr, separator) {
      return arr.join(separator);
    },
    limit: function(arr, count, idx) {
      return arr.slice(+idx || 0, +count + (+idx || 0));
    },
    split: function(str, separator) {
      return str.split(separator || ",");
    },
    choose: function(bool, iffy, elsy) {
      return !!bool ? iffy : (elsy || "");
    },
    toggle: function(obj, csv1, csv2, str) {
      return csv2.split(",")[csv1.match(/\w+/g).indexOf(obj + "")] || str;
    },
    sort: function(arr, prop) {
      var fn = function(a, b) {
        return a[prop] > b[prop] ? 1 : -1;
      };
      return [].concat(arr).sort(prop ? fn : undefined);
    },
    fix: function(num, n) {
      return (+num).toFixed(n);
    },
    mod: function(num, n) {
      return (+num) % (+n);
    },
    divisible: function(num, n) {
      return num && (+num % n) === 0 ? num : false;
    },
    even: function(num) {
      return num && (+num & 1) === 0 ? num : false;
    },
    odd: function(num) {
      return num && (+num & 1) === 1 ? num : false;
    },
    number: function(str) {
      return parseFloat(str.replace(/[^\-\d\.]/g, ""));
    },
    url: function(str) {
      return encodeURI(str);
    },
    bool: function(obj) {
      return !!obj;
    },
    falsy: function(obj) {
      return !obj;
    },
    first: function(iter) {
      //return iter.idx === 0;
      if (!(iter instanceof Array)) {
        return false;
      }
      if (iter.length < 1) return false;
      return iter[0];
    },
    last: function(iter) {
      //return iter.idx === iter.size - 1;
      if (!(iter instanceof Array)) {
        return false;
      }
      if (iter.length < 1) return false;
      return iter[iter.length - 1];
    },
    nth: function(iter, n) {
      //return iter.idx === iter.size - 1;
      if (!(iter instanceof Array)) {
        return false;
      }
      if ((isNaN(n)) || (n < 1) || (iter.length < n)) return false;
      return iter[n-1];
    },
    call: function(obj, fn) {
      return obj[fn].apply(obj, [].slice.call(arguments, 2));
    },
    set: function(obj, key) {
      Mark.globals[key] = obj;
      return "";
    },
    log: function(obj) {
      console.log(obj);
      return obj;
    },
    path: function(obj, params, path) {
      return arguments[arguments.length - 1].io._path_;
    },
    json: function(obj) {
      return JSON.stringify(obj);
    }
  };

  // Shim for IE.
  if (typeof String.prototype.trim !== "function") {
    String.prototype.trim = function() {
      return this.replace(/^\s+|\s+$/g, "");
    }
  }

  // Export for Node.js and AMD.
  if (typeof module !== "undefined" && module.exports) {
    module.exports = Mark;
  } else if (typeof define === "function" && define.amd) {
    define(function() {
      return Mark;
    });
  }
  // Exported api:
  window.Mark = {
    render: function() {
      return Mark.render.apply(Mark, arguments);
    },
    compile: function() {
      return Mark.compile.apply(Mark, arguments);
    },
    registerHelper: function() {
      return Mark.registerHelper.apply(Mark, arguments);
    },
    unregisterHelper: function() {
      return Mark.unregisterHelper.apply(Mark, arguments);
    },
    registerGlobal: function() {
      return Mark.registerGlobal.apply(Mark, arguments);
    },
    unregisterGlobal: function() {
      return Mark.unregisterGlobal.apply(Mark, arguments);
    },
    registerPartial: function() {
      return Mark.registerPartial.apply(Mark, arguments);
    },
    unregisterPartial: function() {
      return Mark.unregisterPartial.apply(Mark, arguments);
    }
  };

})(window);
