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
  * Template Languages
  */

var TemplateLanguages = {
  jsonml: 'template/jsonml',
  xml: 'template/xml',
  html: 'template/html'

};

/** 
  * Template Class
  */

var Template = (function() {

    function Template(template, settings, target) {

      this.template = template;

      this.settings = $.extend({}, {
        language: TemplateLanguages.jsonml,  
      }, Booty.$.is.object(settings) ? settings : {language: settings});

      this.target = target ? target : $("<template>");

      this.__parse();

    }

    Template.prototype = {

      // (instance)
      instance: false,

      // (__parse)
      __parse: function() {

        var content = false;

        switch(this.settings.language) {

          case TemplateLanguages.jsonml:

            this.__parsejsonml(this.template, this.target);

            return this.target.outerHtml();

            break;
           
        }
      },

      // (__parsejsonml)
      __parsejsonml: function(root, parent) {

        var that = this;

        if(!Booty.$.is.array(root)) return false;


        root.forEach(function(node) {

          switch(true) {
            case Booty.$.is.array(node) && Booty.$.is.string(node[0]):

              var attributes = $.extend({}, {_class: false}, Booty.$.is.object(node[1]) ? node[1] : {}),
                  classes = attributes._class ? attributes._class : false;

              delete attributes._class;

              var item = $("<" + node[0] + ">").attr(attributes).addClass(classes).appendTo(parent);

              item.append(Booty.$.is.array(node[2]) ? that.__parsejsonml(node[2], item) : (Booty.$.is.proc(node[2]) ? node[2](item) : node[2]));

              break;

          }

        });
      }

    }

    return Template;

})();
