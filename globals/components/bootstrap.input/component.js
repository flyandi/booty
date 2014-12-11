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
  * (Component) Input
  */

var Component = {

	/** 
	  * Name of Component
	  */

	name: "bootstrap.input",


	/**
	  * Defaults 
	  */

	defaults: {

		/**
		  * (id) the html ID of this component
		  */

		id: false,

		/**
		  * (label) the label of the dropdown
		  */

		label: false,

	},

	/**
	  * (get) primary return function
	  */

	get: function(parent, item, params) {

		/**
		  * Initialize
		  */


		params = $.extend({}, this.defaults, params);

		/**
		  * Prepare item
		  */

		item.addClass("input");

		/* Button */

		var button = $("<button>").addClass("btn btn-default dropdown-toggle").attr($.extend({}, {
			"aria-expanded": "true",
			"data-toggle": "dropdown",
			type: "button"
		}, params.id ? {id: params.id} : false)).appendTo(item);

		button.append($("<l>").append(params.default ? params.default : params.label)).append($("<span>").addClass("caret"));

		/* prepare items */
		var list = $("<ul>").addClass("dropdown-menu").attr({
			role: "menu",
			"aria-labelledby": params.id,
		}).appendTo(item);

		$.each(params.items, function(index, item) {

			switch(true) {

				case Booty.$.is.string(item): item = {label: item}; break;

			}

			/* defaults */
			item = $.extend({}, {tabIndex: -1, href: false, label: false}, item);

			/* link */
			var link = $("<a>").attr($.extend({}, {role: "menuitem", tabindex: item.tabIndex}, 
				item.href ? {href: item.href} : {}
			)).append(item.default ? item.default : item.label);

			list.append($("<li>").attr("role", "presentation").append(link));
		});
		
		return item;
	}

};
