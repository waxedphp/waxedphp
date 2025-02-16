/*!
 * LongPolling
 * Version 1.1.2-2018.05.02
 * Requires javascript :)
 *
 * Examples at: https://github.com/jasterstary/neverending-streaming/tree/master/example
 * Copyright (c) 2017, 2018 JašterStarý
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 *
 */
;(function(window, $) {
  var document = window.document;

  if ( typeof window.CustomEvent !== "function" ) {
    function CustomEvent ( event, params ) {
      params = params || { bubbles: false, cancelable: false, detail: undefined };
      var evt = document.createEvent( 'CustomEvent' );
      evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
      return evt;
     }
    CustomEvent.prototype = window.Event.prototype;
    window.CustomEvent = CustomEvent;
  };

  var AjaxLongPolling = function(url, options) {

    var that = this;
    this.url = url;
    this.data = null;
    this.method = "GET";

    this.tag = 'chunk';
    that._onRequest = function(detail) {
      that._event('longpolling-request', detail);
    };
    that._onChunk = function(chunk, detail) {
      that._event('longpolling-chunk', detail);
    };
    that._onProgress = function(detail) {
      that._event('longpolling-progress', detail);
    };
    that._onSuccess = function(detail) {
      that._event('longpolling-success', detail);
    };
    that._onError = function(detail) {
      that._event('longpolling-error', detail);
    };
    that._onAbort = function(detail) {
      that._event('longpolling-abort', detail);
    };
    that._onComplete = function(detail) {
      that._event('longpolling-complete', detail);
    };
    that._onAllDone = function(detail) {
      that._event('longpolling-all-done', detail);
    };

    this.maxTurns = 1;
    this.interval = 500;
    this.useJSON = false;
    this.prepend = false;

    this.nextReadPos = 0;
    this.listing = [];
    this._events = [];
    this._turn = 0;
    this._chunk = 0;
    this._lastTime = 0;
    this._request_state = 0;
    this._to = false;
    this._pt = false;
    this._stopped = false;

    this._getTime = function() {
      if ((typeof performance == 'object')&&(typeof performance.now == 'function')) {
        return performance.now();
      }
      var d = new Date();
      return d.getTime()
    },

    this._getSpentTime = function() {
      return (this._getTime() - this._lastTime);
    },

    this._event = function(name, detail) {
      // Create the event
      var event = new window.CustomEvent(name, {
        detail:detail,
        bubbles: true,
        cancelable: true
      });
      // Dispatch/Trigger/Fire the event
      document.body.dispatchEvent(event);
    },

    this._logThat = function(theMessage) {
      console.log(theMessage);
    },

    this._eventThat = function(theMessage) {
      this._event(options.onChunk, theMessage);
    },

    this._execThat = function(theMessage) {
      var elem = document.createElement('script');
      elem.innerHTML = theMessage;
      that.listing.push(elem);
      document.body.appendChild(elem);
      if ((that.allowedListLength) && (that.listing.length > that.allowedListLength)) {
        var toKill = that.listing.shift();
        toKill.parentNode.removeChild(toKill);
      };
    },

    this._processWhatCome = function(allMessages) {
      var received = 0;
      do {
        var unprocessed = allMessages.substring(that.nextReadPos);
        //var messageXMLEndIndex = unprocessed.indexOf(that.endTag);
        var messageXMLEndIndex = unprocessed.indexOf(that.enen);
        if (messageXMLEndIndex!=-1) {
          var endOfFirstMessageIndex = messageXMLEndIndex;
          // pick only content of tag:
          //var theChunk = unprocessed.substring(that.startTagLength, endOfFirstMessageIndex);
          var theChunk = unprocessed.substring(0, endOfFirstMessageIndex);
          //endOfFirstMessageIndex = endOfFirstMessageIndex + that.endTagLength;
          endOfFirstMessageIndex = endOfFirstMessageIndex + that.enenLength;
          // decode JSON, wrapped into chunk:
          if (this.useJSON) {
            theChunk = JSON.parse(theChunk.replace(/^\s+|\s+$/gm,''));
          }
          this._chunk++;
          var detail = {
            turn: (this._turn),
            chunk: (this._chunk),
            time: this._getSpentTime(),
            data: theChunk
          };
          if (that._request_state == 1) {
            that._request_state = 2;
          };
          received++;
          // with valid chunk, do the custom function:
          that._onChunk(theChunk, detail);
          // move the position after processed tag:
          that.nextReadPos += endOfFirstMessageIndex;
        }
      } while (messageXMLEndIndex != -1);
      var detail = {
        turn: (this._turn),
        chunks: (this._chunk),
        received: received,
        time: this._getSpentTime()
      };
      that._onProgress(detail);
      if (that._request_state == 3) {
        that._request_state = 4;
      }
    },

    this._doTheStreamWithoutJQuery = function() {
      this.nextReadPos = 0;
      var xhReq = new XMLHttpRequest();
      var method = "GET";
      if (this.method == "POST") {
        method = "POST";
        console.log("POST DATA", this.data);

      };
      xhReq.open(method, that.url + ((that.url.indexOf("?")===-1)?'?':'&') + '_turn_=' + this._turn + '&_t_=' + that._getTime(), true);
      if (this.method == "POST") {
        xhReq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        //xhReq.setRequestHeader('Transfer-Encoding', 'chunked');
        //xhReq.setRequestHeader('Content-type', 'application/octet-stream');
        //xhReq.setRequestHeader('Accept-Encoding', 'identity');

      };
      xhReq.onreadystatechange = function () {
        if(xhReq.readyState === XMLHttpRequest.DONE) {
          clearInterval(that._pt);
          var detail = {
            turn: (that._turn),
            status: xhReq.status,
            statusText: xhReq.statusText
          };
          if (that._request_state == -3) {
            // could not relly on xhReq if aborted:
            detail.status = 0;
            detail.statusText = 'aborted';
          }
          that._request_state = 3;
          that._processWhatCome(xhReq.responseText);
          detail.chunks = that._chunk;
          detail.time = that._getSpentTime();
          if (detail.status === 200) {
            if (that.useJSON) {
              //detail.all = JSON.parse(xhReq.responseText);
              detail.all = xhReq.responseText;
            } else {
              detail.all = xhReq.responseText;
            }
            that._onSuccess(detail);
          } else if (detail.status === 0) {
            that._onAbort(detail);
          } else {
            that._onError(detail);
          }
          that._onComplete(detail);
          that._doTheStream();
        }
      };
      xhReq.send(this.data);
      this._request = xhReq;
      that._pt = setInterval(function(){
        that._processWhatCome(xhReq.responseText);
      }, that.interval);
    },

    this.start = function() {
      this._turn = 0;
      this.resume();
    },

    this.resume = function() {
      this._stopped = false;
      this._doTheStream();
    },

    this._stop = function() {
      this._stopped = true;
      if (this._to) {
        clearTimeout(this._to);
      }
    },

    this.stop = function() {
      this._stop();
      if ((that._request_state != 0)&&(that._request_state != 4)) {
        this._request_state = -3;
        this._request.abort();
      };
    },

    this._doTheStream = function() {
      if ((that._request_state != 0)&&(that._request_state != 4)) {
        return;
      };
      if (this._stopped) return;
      if ((this.maxTurns) && ((this._turn) >= this.maxTurns)) {
        var detail = {
          turns: that._turn
        };
        that._onAllDone(detail);
        this._stop();
        return;
      }
      var t = this._getTime();
      if ((this._lastTime + 900) > t) {
        //console.log('This is not intended use of longpolling stream. Please check the manual.');
        this._to = setTimeout(function(){
          that._doTheStream();
        }, 1000);
        return false;
      };
      this._lastTime = this._getTime();
      this._turn++;
      this._chunk = 0;
      that._request_state = 1;
      var detail = {
        turn: this._turn,
        url: this.url
      };
      that._onRequest(detail);
      that._doTheStreamWithoutJQuery();
    },

    this._setup = function(options) {

      if (typeof options == 'object') {
        if (typeof options.tag == 'string') {
          this.tag = options.tag;
        }
        if (typeof options.maxTurns == 'number') {
          this.maxTurns = options.maxTurns;
        } else if (typeof options.maxTurns == 'boolean') {
          if (!options.maxTurns) {
            this.maxTurns = 0;
          } else {
            this.maxTurns = 1;
          }
        }
        if (typeof options.method == 'string') {
          this.method = options.method;
        }
        if (typeof options.data != 'undefined') {
          this.data = options.data;
        }
        if (typeof options.interval == 'number') {
          this.interval = options.interval;
        }

        if (typeof options.useJSON == 'boolean') {
          this.useJSON = options.useJSON;
        }
        if (typeof options.stopped == 'boolean') {
          this._stopped = options.stopped;
        }

        if (typeof options.onComplete == 'function') {
          this._onComplete = options.onComplete;
        }
        if (typeof options.onSuccess == 'function') {
          this._onSuccess = options.onSuccess;
        }
        if (typeof options.onAbort == 'function') {
          this._onAbort = options.onAbort;
        }
        if (typeof options.onError == 'function') {
          this._onError = options.onError;
        }
        if (typeof options.onAllDone == 'function') {
          this._onAllDone = options.onAllDone;
        }
        if (typeof options.onRequest == 'function') {
          this._onRequest = options.onRequest;
        }
        if (typeof options.onProgress == 'function') {
          this._onProgress = options.onProgress;
        }
        if (typeof options.onChunk == 'function') {
          this._onChunk = options.onChunk;
        } else if (typeof options.onChunk == 'string') {
          switch (options.onChunk) {
            case 'log':
              this._onChunk = this._logThat;
            break;
            case 'exec':
              this._onChunk = this._execThat;
            break;
            default:
              this._onChunk = this._eventThat;
            break;
          };
        }
      };

      this.startTag = '<' + this.tag + '>';
      this.endTag = '</' + this.tag + '>';
      this.startTagLength =  this.startTag.length;
      this.endTagLength =  this.endTag.length;
      this.enen = "\n\n";
      this.enenLength = 2;
    },

    this._init = function() {
      this._setup(options);
      this._doTheStream();
    },  this._init();
  }

  var instances = [];
  // only public methods are exposed:
  window.LongPolling = function(url, options) {
    this.p = instances.length;
    instances[this.p] = new AjaxLongPolling(url, options);

    this.options = function(options) {
      instances[this.p]._setup.call(instances[this.p], options);
      return this;
    },
    this.stop = function() {
      instances[this.p].stop.call(instances[this.p], true);
      return this;
    },
    this.resume = function(options) {
      instances[this.p].resume.call(instances[this.p], options);
      return this;
    },
    this.start = function(options) {
      instances[this.p].start.call(instances[this.p], options);
      return this;
    },
    this.destroy = function() {
      instances[this.p].stop.call(instances[this.p], true);
      delete instances[this.p];
      return null;
    }
  };
})(window, $);
