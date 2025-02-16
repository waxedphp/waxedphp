var AjaxMulti = function(aDataSlots, callback) {

  var that = this;
  this._Slots = {};

  this._start = function(aDataSlots, callbacks) {
    for (var i = 0; i < aDataSlots.length; i++) {
      this._Slots[aDataSlots[i]] = {};
    };
    this._callbacks = callbacks;
  },
  this.received = function(sDataSlotName, oData) {
    if (typeof this._Slots[sDataSlotName] == 'undefined') {
      return;
    }
    this._Slots[sDataSlotName]['data'] = oData;
  },
  this.completed = function(sDataSlotName, xResult) {
    if (typeof this._Slots[sDataSlotName] == 'undefined') {
      //console.log(sDataSlotName, 'undefined');
      return;
    }
    this._Slots[sDataSlotName]['completed'] = true;
    for (var x in this._Slots) {
      if (typeof this._Slots[x]['completed'] == 'undefined') {
        //console.log(x, 'not completed yet', this._Slots);
        return;
      }
    };
    //console.log('CALLBACK', this._Slots, this._callbacks);
    this._callbacks['success'](that);
    this._callbacks['complete'](that);
    //console.log('OK?');
  },
  this.get = function(sDataSlotName) {
    if (typeof this._Slots[sDataSlotName] == 'undefined') {
      return;
    }
    if (typeof this._Slots[sDataSlotName]['data'] == 'undefined') {
      return;
    }
    return this._Slots[sDataSlotName]['data'];
  },
  this.free = function(){
    for (var x in this._Slots) {
      delete this._Slots[x];
    }
  },
  this._start(aDataSlots, callback);
}
