
//import tingle from './tingle-master-2020/src/tingle.js';

;(function ( $, window, document, undefined ) {

    var pluginName = 'dialog',
        _search = '^body',
        _api = ['open', 'close', 'setTemplate'],
        defaults = {
            propertyName: "value",
            modal: false
        },
        opened = {},
        signature = 'xxx',
        cnt = 0,
        inited = false,
        the_modal = false,
        _dialogspace = null,
        _backdrop = null, //singleton elements
        _dialogs = [],
        allowedButtonClasses = [
          'primary', 'secondary',
          'success', 'danger', 'warning', 'info',
          'light', 'dark',
          'link'
        ],
        debug = 0 /* FLAGS: 1=base */
        ;


    function Instance(pluggable,element,dd){
      var that = this;
      this.pluggable = pluggable;
      this.element = element;
      this.o = element;
      this.t = 'tingle';
      this.dd = dd;
      this.name = '';
      this.cfg = {
      };

      this.invalidate = function(RECORD){

      },

      this.setModal = function(b) {

      },

      this.open = function(opt) {
        //console.log('TINGLE-OPEN', opt);
        if (typeof opt.signature != 'string') {
          opt.signature = 'dialog-tingle-'.cnt;
          cnt++;
        }
        if ((opt.signature == 'modal')&&(signature != 'xxx')) opt.signature = signature;
        if (typeof opened[opt.signature] == 'object') {

        } else {
          this._create(opt);
          signature = opt.signature;
          opened[opt.signature] = {
            'yes':1
          };
          //*****************************************************
          that._modal.open();
        };
        var content = that._modal.getContent();
        if ((typeof opt.RECORD == 'object') && (typeof opt.RECORD._dialog_signature_ == 'string')) {
          $(content).attr('id', opt.RECORD._dialog_signature_);
        }
        //console.log(opt);

        //var content = $('div.tingle-modal--visible .tingle-modal-box .tingle-modal-box__content');
        //console.log('CONTENT', content, that._modal.getContent());
        //this.pluggable.initTemplate(content, opt.RECORD);
        //var id = this.pluggable.getDomId(content);
        //console.log('DIALOG pick', id);
        this.pluggable.trigger({
          'element': content,//'#'+id,//
          'action':'display',
          'RECORD': opt.RECORD,
          'template': opt.template,
          'callback': null
        });

      },

      this.setTemplate = function(s, opt){
        //console.log(that);
        //console.log(s);
        //console.log('TINGLE-SET-TEMPLATE', s, opt);

        var footer = that._modal.getFooterContent();
        $(footer).html('');

        if (typeof(opt)!='undefined') {
          //if (typeof(opt['dialog-title'])!='undefined') _dialogs[signature].title.text(opt['dialog-title']);
          //if (typeof(opt['title'])!='undefined') _dialogs[signature].title.text(opt['title']);
          if (typeof(opt['dialog-buttons'])!='undefined') {
            for (var i=0; i<opt['dialog-buttons'].length; i++) {
              var btn = opt['dialog-buttons'][i];
              var cla = 'default';
              //if ((typeof(btn.class)=='string') && (allowedButtonClasses.indexOf(btn.class) > -1)) {
              if (typeof(btn.class)=='string') cla = btn.class;
              //};
              if ((typeof(btn.action)!='undefined')&&(typeof(btn.label)!='undefined')) {
                if (typeof btn.action == 'function') {
                  that._modal.addFooterBtn(btn.label, cla, btn.action);

                  //$('<button type="button" class="btn btn-' + cla + '" >'+btn.label+'</button>')
                  //.appendTo(_dialogs[signature].footer).on('click', dialog, btn.action);
                } else if (btn.action=='dialog-close') {

                  that._modal.addFooterBtn(btn.label, cla, function() {
                      // here goes some logic
                      that._modal.close();
                  });

                } else {

                  //$('<button type="button" class="btn btn-' + cla + '" data-action="'+btn.action+'" >'+btn.label+'</button>')
                  //.appendTo(_dialogs[signature].footer);
                  that._modal.addFooterBtn(btn.label, cla, function() {
                      // here goes some logic

                  });

                };
              }

            };
          };
        }

/*
        // add another button
        that._modal.addFooterBtn('OK!', 'tingle-btn tingle-btn--primary tingle-btn--pull-right', function() {
            // here goes some logic
            that._modal.close();
        });
*/

        that._modal.setContent('');


        var content = that._modal.getContent();
        if ((typeof opt.RECORD == 'object') && (typeof opt.RECORD._dialog_signature_ == 'string')) {
          $(content).attr('id', opt.RECORD._dialog_signature_);
        }

        //var content = $('div.tingle-modal--visible .tingle-modal-box .tingle-modal-box__content');
        //console.log('CONTENT', content, that._modal.getContent());
        //this.pluggable.initTemplate(content, opt.RECORD);
        var id = this.pluggable.getDomId(content);
        //console.log('DIALOG pick', id);
        this.pluggable.trigger({
          'pick': content,//'#'+id,//
          'action':'display',
          'RECORD': opt.RECORD,
          'template': s
        });
        //console.log(typeof content);

        that._modal.open();
        /*
        setTimeout(function(){
          that._modal.checkOverflow();
        }, 300);
        */
        //return _dialogs[signature];
      },

      this.setRecord = function(RECORD){

      },

      this.close = function(signature) {
        if (typeof signature != 'string') return;
        if (signature == 'ALL') {
          var found = false;
          for (var x in opened) {
            //delete opened[x];
            //console.log('TINGLE-IS-OPENED', x);
            found = true;
          }
          //signature = 'xxx';
          if ((found)&&(typeof that._modal == 'object')) {
            that._modal.close();
          };
        } else if (typeof opened[signature] == 'object') {
          delete opened[signature];
          //console.log('TINGLE-CLOSE', signature);
          signature = 'xxx';
          if (typeof that._modal == 'object') {that._modal.close();}
        };
      },

      this.closeIfCan = function() {
        if (defaults.modal) {
          return false;
        };
        this.pluggable.clearClosing();
      },

      this.free = function() {
        this.close();
        if (typeof that._modal == 'object') that._modal.destroy();
      },

      this._create = function(opt) {
        //console.log('TINGLE', $(that.element).data());
        var cfg = {
            footer: true,
            stickyFooter: false,
            closeMethods: ['overlay', 'button', 'escape'],
            //closeMethods: [],
            closeLabel: "Close",
            cssClass: ['custom-class-1', 'custom-class-2'],
            onOpen: function() {
                //console.log('modal open');
            },
            onClose: function(e) {
                //console.log('modal closed',e);
                if (typeof opened[signature] == 'object') {
                  delete opened[signature];
                }
            },
            beforeClose: function() {
                // here's goes some logic
                // e.g. save content before closing the modal
                return true; // close the modal
                return false; // nothing happens
            }
        };
        if (typeof(that.dd.tingleFooter)!='undefined') {
          if (that.dd.tingleFooter == 'false') cfg.footer = false;
          if (that.dd.tingleFooter === false) cfg.footer = false;
        }
        if (typeof(that.dd.tingleStickyFooter)!='undefined') {
          if (that.dd.tingleStickyFooter == 'true') cfg.stickyFooter = true;
          if (that.dd.tingleStickyFooter === true) cfg.stickyFooter = true;
        }
        if (typeof(that.dd.tingleCloseText)!='undefined') {
          cfg.closeLabel = that.dd.tingleCloseText;
        }
        if (typeof(that.dd.tingleCloseMethods)!='undefined') {
          cfg.closeMethods = that.dd.tingleCloseMethods.split(',');
        }
        if (typeof(that.dd.tingleCssClass)!='undefined') {
          cfg.cssClass = that.dd.tingleCssClass.split(',');
        }

        if ((typeof(opt.modal)!='undefined') && (opt.modal)) {
          cfg.closeMethods = [];

        };

        the_modal = new tingle.modal(cfg);
        that._modal = the_modal;
        //console.log('TINGLE', that._modal);
      },

      this.init=function() {
        that._modal = the_modal;
        if (inited) return false;


        inited = true;
      },
      this.init();
    }

    $.waxxx(pluginName, _search, Instance, _api);

})( jQuery, window, document );

