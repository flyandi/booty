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
 * (constants)
 */


/**
  * (Fields) handlers
  */

var Fields = (function(){

    function Fields(origin) {
        // settings
        this.parent = origin;

        // run construct
        this.__construct();
    }

    Fields.prototype = {

        // (constructor)
        __construct: function() {
            this.clear();
           
        },

        /**
          * (attach)
          */
          
        attach: function(target) {

        },


        /**
          * (clear) 
          */

        clear: function() { 
            this.fields = [];
        },


        /**
          * (push)
          */

        push: function(fields) {

            if(!Booty.$.is.array(fields)) return false;

            var that = this;

            this.fields = this.fields.concat(fields);

            return true;

        },


        /** 
          * (render)
          */

        render: function(values, after, target, fields, noempty) {


            var that = this, 
                target = target ? target : this.parent,
                fields = Booty.$.is.array(fields) ? fields : this.fields;


            if(target) {

                /* container */
                var container = $("<fields>"), processed = 0;

                /* empty */
                if(!noempty) $(target).empty();

                /* add */
                $(target).append(container);

                try {
                    // acquire components first
                    this.__acquirecomponents(fields, function() {

                        // cycle 
                        fields.forEach(function(item) {

                            // prepare item
                            item = $.extend({}, {storagedata: null, storage: false, append: false, ref: Booty.ref.none, hooks: false, use: true}, item);

                            if(Booty.$.is.proc(item.use) ? item.use() : (item.use === true)) {

                                // prepare storage
                                if(item.storage) {
                                    item.storagedata = Booty.$.is.defined(values[item.storage]) ? values[item.storage] : null;
                                }

                                // create component
                                var u = false,
                                    m = function(component) {
                                        if(!u) {
                                           u = true;

                                           container.append(component.get(container, $("<div>"), item, that));

                                            if(Booty.$.is.object(item.hooks)) {
                                                container.on("after", function() {
                                                    new Hooks(this, item.hooks);
                                                });
                                            }

                                            if(Booty.$.is.array(item.append)) {

                                                
                                            }

                                            processed++;
                                        }
                                    };

                                var component = Booty.$.components.invoke(item.type, m); 

                                if(component) m(component);
                            }

                        });

                        /*
                         * trigger handlers
                         */

                        WaitFor(function() {
                            return processed >= that.fields.length; 
                        }, function() {

                            values = $.extend({}, {hooks: false}, values);

                            this.hooks = Booty.$.hooks.apply(container, values.hooks);

                            container.triggerHandlers('after');

                            if(Booty.$.is.proc(after)) after();

                        });
                    });

                } catch(e) {
                    alert(e);
                }

            }

            /* failure to render, no active target */
            return false;
        },


         /** 
          * (append)
          */
        append: function(target, params) {

            if(Booty.$.is.array(params.append)) {

                this.render(false, false, target, params.append, true);
            }

        },


        /** 
          * (__acquirecomponents)
          */

        __acquirecomponents: function(fields, callback) {

            var components = [];

            fields.forEach(function(item) {
                if(components.indexOf(item.type) == -1) {
                    components.push(item.type)
                }
            });

            Booty.$.components.load(components, callback);
        },

    }

    return Fields;

})();


/**
  * (wrapper) 
  */

Booty.$.fields = {

    /** 
      * render
      */

    render: function(target, fields, values, after) {
        
        var instance = new Fields(target);

        instance.push(fields);

        return instance.render(values, after);
    }

}
