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
  * API Class
  */

Booty.$.api = {

	/** 
	  * (result)
	  */

	result: {
		ok: 1,
		error: 2,
		autherror: 3,
		authrequired: 4,
		notfound: 5,
		notsupported: 6,
		mismatch: 7,
	},


	/**
	  * (routes)
	  */

	routes: {
		system: '$',
		application: '*'
	},


	/**
	  * (types)
	  */

	types: {
		post: 'POST',
		get: 'GET',
		put: 'PUT',
		api: 'API'
	},


	/**
	  * (consumables) 
	  */

	version: 2,

	last: false,



	/**
	  * (call) executes a call 
	  */

	call: function(request, fields, success, failure) {

		var that = this, ec = true;

		// request defaults
		request = $.extend({}, {type: this.types.api, dataType: 'json', root: true, route: false, request: false}, Booty.$.is.object(request) ? request : {request: request});

		// prepare destination
		var destination = (request.root && request.request.substr(0, 1) != "/" ? "/" : "") + (request.route ? request.route + "/" : "") + request.request;
		
		// operation
		var block = !Booty.$.is.proc(success);

		// prepare handlers
		var handlers = {

			notify: function(data) {

				data = $.extend({}, {status: that.result.notsupported}, Booty.$.is.object(data) ? data : {});

				switch(true) {
					case  data.status == that.result.ok:
						if(Booty.$.is.proc(success)) {

							success(data);
						}
						break;

					default:
						if(ec && Booty.$.is.proc(failure)) {
							ec = failure(data);
						}
						break;
				}
			},

			__parse: function(data) {
				// parse result
				try {

					that.last = data;

					data = JSON.parse(data);

					handlers.notify(data);

					return data;

				} catch(e) {
					handlers.error(false, e.message, e);
				}
			},
			
			dataFilter: function(data, type) {
				handlers.__parse(data);
				return false;
			},

			error: function(xhr, status, error) {
				handlers.notify({
					status: that.result.error, 
					message: error.message ? error.message : status, 
					error: error
				});
			}

		};

		// execute call
		try {

			// need some hooking

			var pull = $.ajax($.extend({}, {
				// automatic handle of syncronizes vs asyncronize
				async: !block,

				// type
				type: request.type,

				// dataType
				dataType: request.dataType,

				// url
				url: destination,

				// headers
				headers: {
					apirequest: true
				},

				// data
				data : fields

			}, block ? {} : handlers));

			// validate blocking operation
			if(block) {
				return handlers.__parse(pull.responseText);
			}

		} catch(e) {
			return handlers.error(false, e.message, e);
		}
	}
};
