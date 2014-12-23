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
  * ScriptContainer
  */



/** 
  * Components Class
  */

Booty.$.components = {

	/** 
	  * Private
	  */

	components: {},
	names: [],


  /**
    * (has)
    */
    
  has: function(name) {
  	return this.names.indexOf(name) !== -1;

  },

  /**
    * (load)
    */

  load: function(components, complete) {

    var that = this, loaded = 0;

    if(!Booty.$.is.array(components)) components = [components];

    components.forEach(function(name) {
     if(!that.has(name)) {
        // create new component
        return that.request(name, function() {
            loaded++;
        });
      } else {
        loaded++;
      }
    });

    WaitFor(function() {
      return loaded >= components.length;
    }, complete); 
  },

  /**
    * (invoke)
    */

  invoke: function(name, complete) {

  	var that = this;

    // check if component is loaded
    if(!this.has(name)) {
      if(!arguments[2]) {
        // create new component
        return this.request(name, function() {
            return that.invoke(name, complete, true);
        });
      }
      return false;
    } 

    var result = this.components[name].invoke();

    if(Booty.$.is.proc(complete)) {
      complete(result);
    }

    return result;
  },


  /**
    * (request)
    */

  request: function(name, after) {

    var that = this;

  	Booty.$.api.call({route: Booty.$.api.routes.system, request: 'component'}, {name: name}, function(content) {
     	// validate result
    	if(content.status == Booty.$.api.result.ok) {
        // register component
        if(content.data.component) {
          // register component
          that.__register(content.data);
          // execute event
          if(Booty.$.is.proc(after)) after(true);          
        }
    	}
   	});
  },

  /**
    * (__register)
    */

  __register: function(component) {
    // initialize
    var that = this, name = component.component;
    // adjust
    if(name.substr(0, 10)=="bootstrap.") name = name.substr(10);
    // verify
    if(this.has(name)) return false;
    // add 
    try {
      this.components[name] = $.extend({}, component, {
        // register script source
        script: Booty.$.base64.decode(component.script),

        // invoke 
        invoke: function() {
          // create new script container
          var container = new Script(this.script, "Component");

          // return instance
          return container.instance;
        }
      });
    } catch(e) {
      alert(e);
    }
    // add to register
    this.names.push(name);
  },

  /**
    * (__clear) 
    */

  __clear: function() { 
      this.components = [];
      this.names
  },

}