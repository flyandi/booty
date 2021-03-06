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
  * (Component) Dropdown
  */

var Component = {

	/** 
	  * Name of Component
	  */

	name: "bootstrap.page",


	/**
	  * Defaults 
	  */

	defaults: {

		/**
		  * (id) the html ID of this component
		  */

		id: false,

		/** 
		  * (content) the content
		  */

		content: false,

		/**
		  * (padding) inner padding of page
		  */

		padding: 20,

	},

	/**
	  * (get) primary return function
	  */

	get: function(parent, item, params) {

		/**
		  * Initialize
		  */

		var that = this;

		this.params = $.extend({}, this.defaults, params);

		/**
		  * Prepare item
		  */

		item.addClass("page container").attr($.extend({}, {
			role: 'presentation'
		}, params.id ? {id: params.id} : false)).appendTo(item);

		if($(".sidebar").length != 0) item.addClass("withsidebar");

		var page = $("<div>").addClass("page-inner").css("padding", this.params.padding).appendTo(item);

		if(params.ref != Booty.ref.none) page.attr("booty-ref", params.ref);

		item.bind({
			after: function() {
				$(window).trigger("pageresize");

				$(this).trigger("update");
			},

			update: function(ev, content) {
				that.update(page, content);
			},

		});
	
		return item;
	},


	/**
	 * (update)
	 */

	update: function(page, content) {

		var that = this;

		var content = content ? content : ParseVariable(this.params.content);

		page.empty();

		switch(true) {

			case Booty.$.is.array(content):



				break;

			default:

				page.append(content);

				break;
		}
	},

};
