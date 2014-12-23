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
  * (Script) 
  */

var Script = (function() {

    function Script(source, type) {
        // settings
        this.$ = false;

        // run construct
        this.invoke(source, type);
    }

    Script.prototype = {

        // (instance)
        instance: false,

        // (invoke)
        invoke: function(source, type) {
            // decode and attach
            this.instance = this.__parse(source, type);

            // validate instance
            if(this.instance) { 
            	switch(true) {
            		case Booty.$.is.object(this.instance): 
            			this.$ = this.instance;
            			break;
            	}
            }
        },

        // (call)
        call: function() {
        	switch(true) {
        		case Booty.$.is.proc(this.instance): 
        			return this.instance.apply(null, arguments ? arguments : null);
        			break;

        		default:
        			return this.instance;
        	}
        },


        // __parse
        __parse: function(code, ident) {
            try {
                // decode

                if(Booty.$.base64.is(code)) {
                    code = Booty.$.base64.decode(code);
                }

                // create function
                var fn = new Function(code + ';return ' + ident + ';');
                
                return fn();


            } catch(e) {    
                alert(e);
            }

            return false;
        }
    }

    return Script;

})();
