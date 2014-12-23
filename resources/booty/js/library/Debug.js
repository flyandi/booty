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
  * Debug Class
  */

Booty.$.debug = {

	/** 
	  * __parse
	  */

	__parse: function(args, cb, d) {
		var r = "";

		$.each(args, function(i, s) {
			var o = (Booty.$.is.proc(cb) ? cb(s) : s);

			r += (Booty.$.is.object(o) ? JSON.stringify(o) : o) + (d ? d : " ");
		});

		return r;
	},

	/** 
	  * Log
	  */

	log: function() {
		try {
			console.log(Booty.$.debug.__parse(arguments));
		} catch(e) {
			
		}
	},

	/**
	  * dump
	  */

	 dump: function() {
	 	alert(JSON.stringify(arguments[0]));
	 },
}


/**
  * globalize
  */

Booty.globalize({
	// object global
	dump: Booty.$.debug.dump,
	log: Booty.$.debug.log
});
