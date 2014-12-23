/**
 * Booty
 * @version: v1.0.0
 * @author: Andy Gulley
 *
 * Created by Andy Gulley. Please report any bug at http://github.com/flyandi/booty
 *
 * Copyright (c) 2014 Andy Gulley http://github.com/flyandi
 *
 * The MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

/** 
  * Dialog Class
  */

var DialogSize = {
  normal: '',
  large: 'modal-lg',
  small: 'modal-sm'
};

var DialogResult = {
  success: true,
  cancel: false,
  none: null,
}



var Dialog = (function() {

    function Dialog(settings) {

      this.$ = false;

      this.settings =  $.extend({}, {
        title: false, 
        titleclose: true, 
        body: false,
        buttons: false,
        size: DialogSize.normal,
        storages: false,
        events: {},
      }, settings);

      // run construct
      this.invoke();
    }

    Dialog.prototype = {

      // (instance)
      instance: false,

      // (invoke)
      invoke: function(source, type) {

        var that = this, container = $("<d>");

        new Template([
          ["div", {role: 'dialog', _class: 'modal fade'}, [
            ["div", {_class: 'modal-dialog ' + this.settings.size}, [
              ["div", {_class: 'modal-content'}, [

                /** 
                  * Header
                  */

                ["div", {_class: 'modal-header ' + (!that.settings.body ? 'no-bottom-border' : '')}, function(item) {

                  if(that.settings.titleclose) item.append($("<button>").attr({type: 'button', "data-dismiss": "modal"}).addClass("close").append("&times;"));

                  item.append($("<h4>").addClass("modal-title").append(that.settings.title));

                }],

                /**
                  * Body
                  */

                that.settings.body ? ["div", {_class: 'modal-body'}, function(body) {

                  that.__body(body);

                }] : false, 

                /**
                  * Footer
                  */

                Booty.$.is.array(that.settings.buttons) ? ["div", {_class: 'modal-footer'}, function(item) {

                  that.settings.buttons.forEach(function(params) {

                    params = $.extend({}, {action: false, highlight: Highlight.none}, params);

                    var button = $("<button>").addClass("btn").append(params.label).appendTo(item);

                    if(params.dismiss) {
                      button.attr("data-dismiss", "modal");
                    } else {
                      button.on("touchend click", function(event) {
                        event.stopPropagation();
                        event.preventDefault();

                        that.__action(params);
                      });
                    }

                    if(params.highlight != Highlight.none) {
                      button.addClass("btn" + (params.highlights === false ? "default" : (params.highlight === true ? "primary" : params.highlight)));
                    }

                  });
                }] : false,
              ]]
            ]]
          ]]
        ], TemplateLanguages.jsonml, container);

        this.dialog = container.find("[role=dialog]:first");

        this.dialog.appendTo('body');

      },


      /**
        * (show) shows the dialog
        */

      show: function(cb) {
        
        var that = this;

        this.dialog.on("shown.bs.modal", function() {
          that.update();
          if(Booty.$.is.proc(cb)) cb();
        }).modal();
    
      },


      /**
        * (hide) hides the dialog
        */

      hide: function(cb) {
        this.dialog.on("hidden.bs.modal", function() {
          if(Booty.$.is.proc(cb)) cb();
        }).modal('hide');
      },


      /**
        * (destroy) destroyes the dialog
        */

      destroy: function() {

        var that = this, d = function() {
          that.dialog.remove();
        };

        this.dialog.is(":visible") ? this.hide(d) : d();
      },

      /** 
        * (result)
        */

      result: function(result) {

        var that = this;

        switch(result) {

          case DialogResult.success:

            var d = true;

            if(Booty.$.is.proc(that.settings.events.success)) {
              d = that.settings.events.success(Booty.$.storages.get(this.dialog));
            }

            if(d) this.destroy();
            
            break;
        }

      },


      /**
        * (update)
        */

      update: function(storages) {

        storages = Booty.$.is.object(storages) ? storages : this.settings.storages;

        if(Booty.$.is.object(storages)) {
          Booty.$.storages.set(this.dialog, storages);
        }

      },


      /**
        * (__body)
        */

      __body: function(body) {

          var that = this;

          switch(true) {

            case Booty.$.is.array(this.settings.body):

              Booty.$.fields.render(body, this.settings.body, this.settings.storages);

              break;

            default:

              body.append(this.settings.body);
              break;
          }
      },

      /**
        * (__action)
        */

      __action: function(params) {

        switch(true) {

          /**
            * (Ok) Condition
            */
          
          case (params.highlight == Highlight.primary):

            this.result(DialogResult.success);

            break;
        }
      }

    }

    return Dialog;

})();


/**
  * Dialog Wrapper 
  */

Booty.$.dialog = {

  /** 
    * (invoke)
    */

  alert: function(message, title) {
    var dialog = new Dialog({
      title: title ? title : message, 
      body: title ? '<p>' + message + '</p>' : false,
      /*
      buttons: [
        {label: 'Close', dismiss: true}
      ],*/
      size: DialogSize.small,

      // events
      events: {
        hide: function(dialog) {
          dialog.destroy();
        }
      }
    });
    dialog.show();
  }

}


/**
  * globalize
  */

Booty.globalize({
  // object global
  alert: Booty.$.dialog.alert

});