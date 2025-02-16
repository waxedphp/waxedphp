(function($, window){

  $.fn.classList = function() {return this[0].className.split(/\s+/);};

  $.fn.classVariantList = function(pattern) {
    var r = '(^|\\s)' + pattern + '\\S+';
    //console.log(r);
    var reg = new RegExp(r, 'g');
    var a = this[0].className.split(/\s+/);
    return a.filter(function(name){
      return reg.test(name);
    });
  };

  $.fn.classBootstrapColspan = function(pattern) {
    var r = '(^|\\s)col\-md\-(\\d+)';
    var reg = new RegExp(r, 'g');
    var a = this[0].className.split(/\s+/);
    return a.filter(function(name){
      return reg.test(name);
    }).map(function(name){
      return(/col-md-(\d+)/.exec(name)[1]);
    })[0];
  };

})($, window);
