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
  * Storages Class
  */



var Storages = (function() {

    function Storages(element, settings) {

    	this.element = element ? $(element) : false;

		  this.settings =  $.extend({}, {

		  }, settings);

		}

    Storages.prototype = {


      /** 
        * (get)
        */

      get: function() {

      	var storages = {};

        this.each(function(target, name, data) {

          if(!storages[name]) storages[name] = data;
        });

        return storages;
      },


      /** 
        * (set)
        */

      set: function(storages) {

        this.each(function(target, name, data) {

          if(storages[name]) {
            target.data("@storage", storages[name]).val(storages[name]);
          }

        });
      },


      /**
        * (each)
        */

      each: function(cb) {

        if(!Booty.$.is.proc(cb) || !this.element) return false;

        this.element.find("[data-storage]").each(function() {

          var name = $(this).attr("data-storage"),
              data = $(this).val() ? $(this).val() : $(this).data("@storage");

          cb($(this), name, data ? data : null);

        });

      },

    }

    return Storages;

})();


Booty.$.storages = {

  /** 
    * (apply)
    */

  get: function(element) {
    return (new Storages(element)).get();
  },

  set: function(element, storages) {
    return (new Storages(element)).set(storages);
  }
};