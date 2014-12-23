<?php
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

namespace Booty\Framework;

/**
  * (constants)
  */

define("BOOTY_TAG_IDENT", "booty-");

/**
  * (Enums)
  */


interface HTMLMedia {
	const screen = "screen";
}


interface HTMLValues {
	const true = "true";
	const false = "false";
}


/** 
  * (class) HTML
  */

class HTML extends Primitive {


	/** 
	 * (__construct) 
	 *
	*/

	public function __construct() {


	}


	/** 
	 * (Tag) Creates a tag
	 *
	 */

	static public function Tag($name, $attributes = false, $content = null) {
		return Tag(BOOTY_TAG_IDENT . $name, $attributes, $content, $content == null);
	}


	/** 
	 * (Stylesheet) Creates a quick code snippet for a stylesheet 
	 *
	 */

	static public function Stylesheet($source, $media = false) {
		return Tag("link", Extend(
			// default values
			array(
				"rel" => "stylesheet", 
				"type" => "text/css", 
				"href" => $source
			),
			// (optional) media
			$media !== false ? array("media" => $media) : false
		), false, true);
	}

}

