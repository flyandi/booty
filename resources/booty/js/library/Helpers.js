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
  * (macro) triggerHandlers
  */

jQuery.fn.triggerHandlers = function(type, data) {
	if(type) {
		return this.each(function() {
			$(this).triggerHandler(type, data);
			$(this).children().triggerHandlers(type, data);
		});
	}
}

jQuery.fn.outerHtml = function() {
	return $("<div>").append($(this).clone()).html();s
}

jQuery.fn.align = function(align) {

	switch(align) {
		case Align.auto: 
			align = {left: Align.auto, left: Align.auto, right: Align.auto, bottom: Align.auto};
			break;

		case Align.none:
			return;
			break;
	}

	align = $.extend({}, {absolute: true, margin: 10, left: false, right: false, top: false, bottom: false, width: false, height: false}, align);

	return this.each(function() {

		var target = $(this);

		target.css($.extend({}, {
			margin: align.margin
		}, align.absolute ? {position: 'absolute'} : {}));

		$.each(['left', 'top', 'bottom', 'right', 'width', 'height'], function(index, value) {
			target.css(value, align[value] ? align[value] : "");
		});

	});
}

/**
  * (macro) $$$ Shortcut to create jQuery Elements
  */

var $$$ = function(tag, css, attr, content) {
	return $("<" + tag + ">").addClass(css).attr(attr).append(content ? content : "");
};


/**
  * (macro) DefaultValue
  */

var DefaultValue = function(s, d) {
	return s !== false && s !== null && s !== '' ? s : d;
};

/**
  * (macro) WaitFor
  */

var WaitFor = function(condition, callback, params) {

	params = $.extend({}, {timeout: 5000, start: (new Date()).getTime()}, params);
	
	var result = Booty.$.is.proc(condition) ? condition() : false;

	if(result) {
		if(Booty.$.is.proc(callback)) callback();
	} else {
		if(params.start + params.timeout > (new Date()).getTime()) {
			setTimeout(function() {
				WaitFor(condition, callback, params);
			}, 1);
		} 
	}
};

/**
  * (macro) ParseVariable
  */

var ParseVariable = function(d) {
	return Booty.$.is.proc(d) ? d() : d;
};
