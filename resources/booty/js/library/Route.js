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
  * Route Class
  */

var RouteState = {
  unresolved: 0,
  resolved: 1,
  pushed: 2,
}

var Route = (function() {

    function Route(location, settings) {

      this.location = location;

      this.state = RouteState.unresolved;

      this.settings =  $.extend({}, {
        hooks: false
      }, settings);

      this.invoke();
    }

    Route.prototype = {

      invoke: function() {
        if(this.settings.virtual) {

          this.destination = this.__destination();

          this.state = RouteState.resolved;
        }
        
      },

      navigate: function() {
        
      },


      handled: function() {
        return this.state == RouteState.resolved;
      },

      setlocation: function(location, title, obj) {
        if(history) {
          history.pushState(obj ? obj : {}, title ? title : document.title, location);
        }
      },

      getlocation: function() {
        return (window.location.href.toString().split(window.location.host)[1])
      },

      resolve: function(location) {
        location = (!location ? this.getlocation() : location).replace(/\/+$/, "");

        return location.length > 1 ? location : false;
      }
    }

    return Route;

})();