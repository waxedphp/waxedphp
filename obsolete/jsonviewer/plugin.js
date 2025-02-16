
//import './jquery.jsonview.js';

;(function ( $, window, document, undefined ) {

    var pluginName = 'jam/jsonview',
        defaults = {
            propertyName: "value"
        };

  
    function Instance(pluggable,element,dd){
    this.pluggable = pluggable;
    this.element = element;
    this.o = element;
    this.t = 'jsonview';
    this.dd = dd;
    this.name = '';
    this.cfg = {
    collapsed:true
    };

    this.invalidate = function(RECORD){
      return;
      /*
      $(this.element).removeClass('invalid');
      this.setRecord(RECORD);
      if(typeof(this.dd.name)=='undefined')return false;
      if(this.dd.name in RECORD){
        $(this.element).addClass('invalid');
        $(this.element).before('<label class="invalid">'+RECORD[this.dd.name]+'</label>');      
      };
      */
    },
    
    this.filter = function(o) {
      return o;
    },
    
    this.setRecord = function(RECORD){
        var rec={};
        if(typeof(this.dd.name)!='undefined'){
          if(typeof(RECORD[this.dd.name])!='undefined'){
            rec = RECORD[this.dd.name];
          };
          $(this.element).JSONView(rec,this.cfg);
        } else if(typeof(this.dd.names)!='undefined'){
          var a = this.dd.names.split(',');
          var found = false;
          for (var i = 0; i < a.length; i++) {
            var key = a[i].trim(); 
            if(typeof(RECORD[key])!='undefined'){
              rec[key] = RECORD[key];
              found = true;
            };          
          };
          if (found) {
            $(this.element).JSONView(this.filter(rec),this.cfg);
          };
           
        } else {
          rec = RECORD;
          $(this.element).JSONView(this.filter(rec),this.cfg);
        };
        
    },
          
      this.init=function(){
        var that = this;
        if(typeof(this.dd.collapsed)!='undefined'){
          this.cfg.collapsed=this.dd.collapsed;
        };
      },
      this.init();
  }

    if (typeof(document.jammin) == 'undefined') {
      document.jammin = {};
    };
    document.jammin[pluginName] = {
      search:'.jsonviewer',
      getInstance:function(plug,elem,data) {
        //var data = $(elem).data();
        if(!data['plugin_'+pluginName]){
          $(elem).trigger('jam-plugin-instance-create', pluginName);
          var o = new Instance(plug,elem,data);
          $.data(elem,'plugin_'+pluginName,o);
              return o;
            }else{
          return data['plugin_'+pluginName];
        }
      }
    };

})( jQuery, window, document );
