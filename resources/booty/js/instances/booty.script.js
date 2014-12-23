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
  * (script) instance
  */

var __booty_instance_script = (function(){

    function __booty_instance_script(origin) {
        // settings
        this.parent = origin;

        // run construct
        this.__construct();
    }

    __booty_instance_script.prototype = {

        // (private)


        // (constructor)
        __construct: function() {
            
        },


        // (attach)
        attach: function(target) {
            // initialize
            var that = this;

            var parent = $("<section>");

            $(target).replaceWith(parent);

            this.invoke(
                // decode assets
                $(target).attr("source"),
                // assign type
                $(target).attr("type"),
                // target
                parent,
                // runtime
                {
                    invoke: $(target).attr("invoke") == "true"
                }
            );
        },

        // (resolve)
        resolve: function(target, parent, payload) {


            target = target ? target : (function() {

                var l = parent ? $(parent) : $(body);

                l = l.find("[booty-ref=" + Booty.ref.content + "]");

                return l.length != 0 ? l : false;           

            })();


            if(!target) return false;



            if(Booty.$.is.object(payload)) {

                payload = $.extend({}, {status: false, data: false}, payload);

                var data = payload.data;

                switch(true) {


                    /**
                      * Detection for scripts
                      */

                    default:

                        if(data.type && data.source) {

                            var script = new __booty_instance_script(false);

                            script.invoke(data.source, data.type, target);

                            return true;
                            
                        }

                        break;
                }
            }

            return false;

        },


        // (invoke)
        invoke: function(source, type, target, runtime) {

            var that = this;

            runtime = $.extend({}, {invoke: true}, runtime);

            // decode and attach
            try {
                this.instance = (new Script(source, type)).$;

                // verify
                if(this.instance) {

                    // assign variables
                    this.instance.parent = $(target);
                    
                    /** (call) */
                    this.instance.call = function(action, data, success, failure) {
                        return Booty.$.api.call(window.location.pathname, $.extend({}, data, {action: action}), success, failure);
                    }

                    /** (route) */
                    this.instance.route = function(location, data, callback) {

                        var route = new Route();

                        location = route.resolve(location);

                        if(location) {

                            Booty.$.api.call(location, data, function(result) {


                                if(that.resolve(false, that.instance.parent, result)) {

                                    route.setlocation(location);

                                }

                                return true;

                            }, function(error) {

                                /** Need to implement meaningful messages here */
                                Booty.$.alert('The requested resource is not available (' + error.message + ')', 'Error');

                            });
                        }
                    }   

                    /** (readfile) */
                    this.instance.readfile = function(f) {
                        return this.call('readfile', {filename: f}, true);
                    }
                    
                    /** (has) */
                    this.instance.has = function(n) {
                        return typeof(this[n])=="function";
                    }

                    /** (returncontent) */
                    this.instance.returncontent = function(content, values, after) {
                        switch(true) {
                            case $$.is.proc(content):
                                this.parent.append(content());
                                break;

                            case $$.is.object(content): 
                                return $$.fields.render(this.parent, content, values, after);
                                break;

                            case $$.is.string(content):
                                this.parent.append(content);
                                break;

                            default:
                                return false; 
                        }

                        if($$.is.proc(after)) after();

                        return true;
                    }

                    /** (error) */
                    this.instance.error = function(error) {
                        Booty.$.alert(error);
                    }
                    
                    // initialize
                    if(this.instance.has("initialize")){
                        this.instance.initialize();
                    }

                    // invoke
                    if(runtime.invoke && this.instance.has("main")) {
                        this.instance.main();
                    }
                }
            } catch(e) {

            }
        },
    }

    return __booty_instance_script;

})();


/**
 * (register instance)
 */

Booty.register('script', {

    // (invoke)
    invoke: function(origin) {
        // create new instance
        return new __booty_instance_script(origin);
    },
});
