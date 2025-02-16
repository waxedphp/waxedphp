(function(window, $) {
  var document = window.document;

  var AjaxNeverendingStreaming = function(url, selector, options) {

    var that = this;
    this.url = url;

    if (this.useJQuery) {
      this.element = $(selector);
    } else {
      this.element = selector;
    }

    this.tag = 'message';
    that._onMessage = that._drawThat;
    this.allowedListLength = 10;
    this.interval = 1000;
    this.useJQuery = false;
    this.useJSON = false;
    this.prepend = false;

    this.nextReadPos = 0;
    this.listing = [];
    this._turn = 0;
    this._lastTime = 0;

    this._to = false;
    this.pollTimer = false;
    this._stopped = false;

    this._logThat = function(theMessage) {
      console.log(theMessage);
    },

    this._drawThat = function(theMessage) {
      if (that.useJQuery) {
        if (that.prepend) {
          that.listing.push($('<div>' + theMessage + '</div>').prependTo(that.element));
        } else {
          that.listing.push($('<div>' + theMessage + '</div>').appendTo(that.element));
        };
        if (that.listing.length > that.allowedListLength) {
          var toKill = that.listing.shift();
          $(toKill).remove();
        };
      } else {
        var div = document.createElement('div');
        div.innerHTML = theMessage;
        that.listing.push(div);
        var next = that.element.getElementsByTagName('div')[0];
        if (that.prepend && next) {
          that.element.insertBefore(div, next);
        } else {
          that.element.appendChild(div);
        }
        if (that.listing.length > that.allowedListLength) {
          var toKill = that.listing.shift();
          toKill.parentNode.removeChild(toKill);
        };
      };
    },

    this._processWhatCome = function(allMessages) {
      do {
        if (this._stopped) return;
        var unprocessed = allMessages.substring(that.nextReadPos);
        var messageXMLEndIndex = unprocessed.indexOf(that.endTag);
        if (messageXMLEndIndex!=-1) {
          var endOfFirstMessageIndex = messageXMLEndIndex;
          // pick only content of tag:
          var theMessage = unprocessed.substring(that.startTagLength, endOfFirstMessageIndex);
          endOfFirstMessageIndex = endOfFirstMessageIndex + that.endTagLength;
          // decode JSON, wrapped into message:
          if (this.useJSON) {
            theMessage = JSON.parse(theMessage);
          }
          // with valid message, do the custom function:
          that._onMessage(theMessage);
          // move the position after processed tag:
          that.nextReadPos += endOfFirstMessageIndex;
        }
      } while (messageXMLEndIndex != -1);
    },

    this._doTheStreamWithoutJQuery = function() {
      this.nextReadPos = 0;
      var xhReq = new XMLHttpRequest();
      xhReq.open("GET", that.url + ((that.url.indexOf("?")===-1)?'?':'&') + '_turn_=' + this._turn, true);
      xhReq.onreadystatechange = function () {
        if(xhReq.readyState === XMLHttpRequest.DONE){
          clearInterval(that.pollTimer);
          that._doTheStream();
        };
        if(xhReq.readyState === XMLHttpRequest.DONE && xhReq.status === 200) {

        } else {

        }
      };
      xhReq.send(null);
      this._turn++;
      that.pollTimer = setInterval(function(){
        that._processWhatCome(xhReq.responseText);
      }, that.interval);
    },

    this._doTheStreamWithJQuery = function(){
      this.nextReadPos = 0;
      $.ajax(this.url + ((that.url.indexOf("?")===-1)?'?':'&') + '_turn_=' + this._turn, {
          xhrFields: {
            onprogress: function(e){
              that._processWhatCome(e.currentTarget.response);
            }
          }
      })
      .done(function(data) {
        that._doTheStream();
      })
      .fail(function(data) {
          //console.log('Error: ', data);
      });
      this._turn++;
    },

    this.stop = function(b) {
      if (b) {
        this._stopped = true;
        if (this.pollTimer) {
          clearInterval(this.pollTimer);
        }
        if (this._to) {
          clearTimeout(this._to);
        }
      } else {
        this._stopped = false;
        this._doTheStream();

      }

    },

    this._doTheStream = function() {
      if (this._stopped) return;
      var d = new Date();
      if ((this._lastTime + 900) > d.getTime()) {
        //console.log('This is not intended use of neverending stream. Please check the manual.');
        this._to = setTimeout(function(){
          that._doTheStream();
        }, 1000);
        return false;
      };
      this._lastTime = d.getTime();
      if (this.useJQuery) {
        this._doTheStreamWithJQuery();
      } else {
        this._doTheStreamWithoutJQuery();
      }
    },

    this._setup = function(options) {

      if (typeof options == 'object') {
        if (typeof options.tag == 'string') {
          this.tag = options.tag;
        }
        if (typeof options.allowedListLength == 'number') {
          this.allowedListLength = options.allowedListLength;
        }
        if (typeof options.interval == 'number') {
          this.interval = options.interval;
        }
        if (typeof options.useJQuery == 'boolean') {
          this.useJQuery = options.useJQuery;
        }
        if (typeof options.useJSON == 'boolean') {
          this.useJSON = options.useJSON;
        }
        if (typeof options.stopped == 'boolean') {
          this._stopped = options.stopped;
        }
        if (typeof options.prepend == 'boolean') {
          this.prepend = options.prepend;
        }
        if (typeof options.onMessage == 'function') {
          this._onMessage = options.onMessage;
        } else if (typeof options.onMessage == 'string') {
          switch (options.onMessage) {
            case 'log':
              this._onMessage = this._logThat;
            break;
            case 'draw':
              this._onMessage = this._drawThat;
            break;
            case 'exec':
              this._onMessage = this._execThat;
            break;
            default:
              this._onMessage = this._eventThat;
            break;
          };

        }
      };

      this.startTag = '<' + this.tag + '>';
      this.endTag = '</' + this.tag + '>';
      this.startTagLength =  this.startTag.length;
      this.endTagLength =  this.endTag.length;
    },

    this._init = function() {
      this._setup(options);
      this._doTheStream();
    },  this._init();
  }

  var instances = [];
  // only public methods are exposed:
  window.AjaxNeverendingStreaming = function(url, selector, options) {
    this.p = instances.length;
    instances[this.p] = new AjaxNeverendingStreaming(url, selector, options);

    this.options = function(options) {
      instances[this.p]._setup.call(instances[this.p], options);
      return this;
    },
    this.stop = function() {
      instances[this.p].stop.call(instances[this.p], true);
      return this;
    },
    this.resume = function() {
      instances[this.p].stop.call(instances[this.p], false);
      return this;
    }
  };
})(window, $);
