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


var 
    // Structs
    BOOTY_ATTRIBUTES = ['b-', 'booty-'],

    BOOTY_TAGS = {
        div: 'div',
        form: 'form',
        css: 'link',
        link: 'link',
        script: 'script',
    },

    BOOTY_CLASS_OPERATION = {
        add: 0,
        remove: 1,
        replace: 2,
        toggle: 3
    },

    BOOTY_VERBAL = true;



/**
 * (Booty) main object
 */


var Booty = {

    /** 
      * (privates)
      */

    modules: {},

    /**
      * (refs)
      */

    ref: {
        none: false,
        content: 'content'
    },

    /**
      * (functions) universal accessible
      */

    $: {},


    /** 
      * (globalize) globalizes a booty module
      */

    globalize: function(global, superglobal) {

        $.each([global, superglobal], function(index, map) {
            if(map) {
                $.each(map, function(name, fn) {
                    if(index == 1) {
                        window[name] = fn;
                    } else {
                        Booty.$[name] = fn;
                    }
                });
            }
        });

    },


    /**
      * (register) registers a module
      */

    register: function(name, module) {
        // initialize
        var that = this;

        // avoid overwrite
        if(this.modules[name]) return false;

        // process module
        if(module.require) {
            module.require.forEach(function(lib) {
                // single
                if(lib.condition && lib.condition()) return false;
                // attach 
                that.__attachscript(lib.src);
            });
        }

        // assign
        this.modules[name] = module;
    },


    // (__load) 
    __load: function() {
        // initialize
        var that = this;

        // autodetection
       	$.each({
       		script: function() {
       			return "script";
       		}
       	}, function(className, classMethod) {
       		// prepare objects
       		$("booty-" + className).each(function() {
       			// assign
       			that.__attach(typeof(classMethod) == "function" ? classMethod() : classMethod, this);
       		})
        });
    },


    // (__attach)
    __attach: function(name, target) {
        // initialize

        var that = this,
            module = this.__requiremodule(name, function() {
                that.__attach(name, target, true);
            }, arguments[2]);

        // verify
        if(!module) return;

        // try module
        try {
            // create instance
            var instance = module.invoke(this);
            
            // attach instance
            instance.attach(target);

        } catch(e) {
            alert(e);
        }   
    },

    // (__requiremodule)
    __requiremodule: function(name, cb) {
        // verify module
        var that = this, module = this.modules[name] ? this.modules[name] : false;

        // lazy load
        if(!module && !arguments[2])  {
            // attach
            this.__attachscript('/resources/booty/booty.' + name + '.js', function() {
                // execute callback
                if(typeof cb == "function") {
                    cb();
                }
            });
            // exit
            return false;
        }
        // return module
        return module;
    },

	// (__attachscript)
    __attachscript: function(src, cb, tag) {
        var head = document.querySelector('head'),
            element = false;

        // switch tag type
        switch(tag) {
            case BOOTY_TAGS.css:
                element = document.createElement('link');
                element.href = src;
                element.rel = "stylesheet";
                break;
            default:
                element = document.createElement('script');
                element.type= 'text/javascript';
                element.src= src;
                break;
        }
        // event
        if(typeof cb == "function") element.onload = function() {
            cb();
        };
        // run
        head.appendChild(element);
    },


    // (__prototypes)
    __prototypes: function() {

        // straps::rid
        Booty.rid = function(c) {
            return (c ? c : 'xxxysxx4xxxxxxxxxxx').replace(/[xy]/g, function(c){var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8); return v.toString(16);}).toUpperCase();
        };

        // globalization
        window.$$ = this.$;
    },

    // (__report)
    __report: function(e) {
        if(BOOTY_VERBAL) {
            console.log(e);
        }
    }

};


/** 
  * (runtime)
  */

(function() {
    // load prototypes
    Booty.__prototypes();

    // load 
    window.addEventListener("load", function() {
        setTimeout(function() {
            Booty.__load();
        }, 1);
    }, false);
})();