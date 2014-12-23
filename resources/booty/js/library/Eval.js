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
  * Eval / Is Class
  */

Booty.$.eval = {

	/** 
	  * Log
	  */

	is: {

		/** (proc) */
		proc: function() {
			return typeof(arguments[0]) == "function";
		},

		/** (object) */
		object: function() {
			return typeof(arguments[0]) == "object";
		},

		/** (array) */
		array: function() {
			return Object.prototype.toString.call(arguments[0]) === '[object Array]';
		},

		/** (string) */
		string: function() {
			return typeof(arguments[0]) == "string";
		},

		/** (number) */
		number: function() {
			return typeof(arguments[0]) == "number";
		},

		/** (boolean) */
		boolean: function() {
			return typeof(arguments[0]) == "boolean";
		},

		/** (defined) */
		defined: function() {
			return typeof(arguments[0]) != "undefined";
		}

	}
}


/**
  * globalize
  */

Booty.globalize({
	// object global
	is: Booty.$.eval.is
});
