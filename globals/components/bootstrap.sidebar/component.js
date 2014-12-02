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
  * (Component) bootstrap.sidebar
  */


var Component = {

	/** 
	  * Name of Component
	  */

	name: "bootstrap.sidebar",


	/**
	  * Defaults 
	  */

	defaults: {

		/** 
		  * (items) items of the navbar
		  */

		items : [],

		/**
		  * (fixed)
		  */

		fixed: false,
	},

	/**
	  * (get) primary return function
	  */

	get: function(parent, item, params) {

		var dest = parent.find("nav");

		/* Initialize */
		params = $.extend({}, this.defaults, params);

		/* Prepare */
		var bar = $("<div>").addClass("navbar-default sidebar").attr({role: "navigation"}).appendTo(dest),
			container = $("<div>").addClass("sidebar-nav navbar-collapse").appendTo(bar);


		/* Render items */
		this.__render(container, params.items);


		/* Bind events */
		bar.bind({
			after: function() {
				$(this).find("[data-target=sidebar]").metisMenu();

			},
		});

		/* return */
		return false;
	},

	/**
	  * (__render)
	  */


	__render: function(container, items, params) {

		if(!Booty.$.is.array(items)) return;


		params = $.extend({}, {level: 0}, params);

		var that = this,
			list = $("<ul>").addClass("nav").attr(params.level == 0 ? {"data-target": "sidebar"} : {}).appendTo(container);

		switch(params.level) {
			case 2: list.addClass("nav-third-level"); break;
			case 1: list.addClass("nav-second-level"); break;
		}

		$.each(items, function(index, item) {

			that.__renderItem($("<li>").appendTo(list), item, params.level);
		});
	},

	/**
	  * (__renderItem)
	  */

	__renderItem: function(container, item, level) {

		item = $.extend({}, {label: false, href: false, active: false, items: false, use: true}, item);

		if(item.use === false || level > 2) return;


		var link = $("<a>").attr(item.href ? {href: item.href} : {"data-target": "#"}).append(this.__icon(item.icon)).append("&nbsp;").append(item.label).appendTo(container);

		this.__common(container, item, {align: false});

		if(Booty.$.is.array(item.items)) {

			/**
			 * (level split)
			 */

			 link.append($("<span>").addClass("fa arrow"));

			 this.__render(container, item.items, {level: level + 1});

		}
	},


	/**
	  * (__common)
	  */

	__common: function(element, params, exclude) {

		params = $.extend({}, {active: false}, params ? params : {}, exclude ? exclude : {});

		if(params.active) element.addClass("active");
	},

	/**
	  * (__icon)
	  */

	__icon: function(icon, params) {

		if(!icon) return "";
		
		params = $.extend({}, {fixed: false}, params);

		return $("<i>").addClass("fa fa-" + icon).addClass(params.fixed ? "fa-fw" : "");


	},



};
