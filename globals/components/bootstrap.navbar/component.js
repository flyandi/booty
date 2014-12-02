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
  * (Component) NavBar
  */


var Component = {

	/** 
	  * Name of Component
	  */

	name: "bootstrap.navbar",


	/**
	  * Defaults 
	  */

	defaults: {

		/**
		  * (id) the html ID of this component
		  */

		id: false,

		/**
		  * (title) the title of this jumbotron
		  */

		logo: false,

		/**
		  * (message) the message of this jumbotron
		  */

		logolink: false,

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

		/* Initialize */
		params = $.extend({}, this.defaults, params);

		/* Prepare */
		var nav = $("<nav>").addClass("navbar navbar-default").attr({role: "navigation"}),
			container = $("<div>").addClass("container-fluid").appendTo(nav),
			header = $("<div>").addClass("navbar-header").appendTo(container);

		if(params.fixed) nav.addClass("navbar-fixed-" + params.fixed);

		/* Logo */
		if(params.logo) {
			$("<a>").addClass("navbar-brand").attr({href: DefaultValue(params.logolink, "#")}).append(params.logo).appendTo(header);
		}

		/* Render items */
		this.__render(container, params.items);

		/* Bind events */
		nav.bind({
			after: function() {
				$(this).find("[data-toggle=dropdown]").dropdown();

			},
		});

		/* return */
		return nav;
	},

	/**
	  * (__render)
	  */

	__render: function(container, items, params) {

		if(!Booty.$.is.array(items)) return;

		var that = this;

		params = $.extend({}, {wrap: false, exclude: false, extend: false}, params);

		$.each(items, function(index, item) {

			that.__renderItem(params.wrap ? $(params.wrap).appendTo(container) : container, $.extend({}, params.extend ? params.extend : {}, item, params.exclude ? params.exclude : {}));
		});
	},

	/**
	  * (__renderItem)
	  */

	__renderItem: function(container, item) {

		var that = this;

		item = $.extend({}, {use: true}, item);

		if(item.use === false) return;
		
		switch(true) {

			/**
			 * (list)
			 */

			case item.list: 
				
				item = $.extend({}, {items: [], align: Align.left}, item);

				var list = $("<ul>").addClass("nav navbar-nav").appendTo(container);

				this.__common(list, item);

				this.__render(list, item.items, {wrap: '<li>', exclude: {align: false}})	;

				break;

			/**
			 * (link)
			 */

			case item.link:

				item = $.extend({}, {href: false, icon: false, pulltext: false, active: false, label: false}, item);

				var link = $("<a>").attr({href: item.href}).append(this.__icon(item.icon)).append(item.label).appendTo(container);

				if(item.pulltext) link.append($("<span>").addClass("pull-right text-muted small").append(item.pulltext));

				this.__common(container, item, {align: false});

				break;

			/**
			  * (divider)
			  */

			case item.divider:
		
				container.addClass("divider");

				break;

			/**
			 * (dropdown)
			 */

			case item.dropdown:

				item = $.extend({}, {caret: true, icon: false, active: false, label: false, items: []}, item);

				var dropdown = $("<a>").addClass("dropdown-toggle").attr({
					"data-target": "#",
					"data-toggle": "dropdown",
					"role": "button",
					"aria-expanded": false,
				}).append(this.__icon(item.icon, {fixed: true})).append(item.label).append(item.caret ? $("<span>").addClass("caret") : "").appendTo(container);

				var list = $("<ul>").addClass("dropdown-menu").attr({role: "menu"}).appendTo(container);

				this.__render(list, item.items, {wrap: "<li>", exclude: {align: false}});

				this.__common(container, item);

				/* special cases */
				if(item.special) list.addClass("dropdown-" + item.special);

				container.addClass("dropdown");

				break;

			/**
			 * (form)
			 */

			case item.form:

				item = $.extend({}, {items: [], buttons: []}, item);

				var form = $("<form>").addClass("navbar-form").appendTo(container);

				this.__common(form, item);

				this.__render($("<div>").addClass("form-group").appendTo(form), item.items);

				this.__render(form, item.buttons, {extend: {button: true}});

				/* bind local events */

				form.bind({}, {
					submit: function() {
						return false;
					}
				});

				break;

			/**
			 * input
			 */

			case item.input: 

				item = $.extend({}, {type: 'text', placeholder: false}, item);

				var input = $("<input>").attr("type", item.type).addClass("form-control").appendTo(container);

				input.attr($.extend({}, 
					item.placeholder ? {placeholder: item.placeholder} : {}
				));

				break;

			/**
			 * button
			 */

			case item.button:

				item = $.extend({}, {label: false, submit: false}, item);

				var button = $("<button>").addClass("btn btn-default").append(item.label).appendTo(container);

				if(item.submit) button.attr("type", "submit");
				break;

			/**
			 * text
			 */

			case item.text: 


				var text = $("<div></div>").append(item.text).appendTo(container);

				this.__common(text, item);

				break;
		}
	},


	/**
	  * (__common)
	  */

	__common: function(element, params, exclude) {

		params = $.extend({}, {role: false, active: false, align: Align.left}, params ? params : {}, exclude ? exclude : {});
		
		if(params.align) element.addClass("navbar" + params.align);
		if(params.active) element.addClass("active");
		if(params.role) element.attr("role", params.role);

	},

	/**
	  * (__icon)
	  */

	__icon: function(icon, params) {

		if(!icon) return "";
		
		params = $.extend({}, {fixed: true}, params);

		return $("<i>").addClass("fa fa-" + icon).addClass(params.fixed ? "fa-fw" : "");


	},



};
