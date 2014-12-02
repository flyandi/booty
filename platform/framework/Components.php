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

interface ComponentsPath {
	const platform = "../globals/components/";
	const application = "/components/";
}


/** 
  * (class) View
  * Manages a view
  */

class Components extends Primitive {

	/** 
	 * (private)
	*/


	/** 
	 * (__construct) 
	 *
	 */

	public function __construct($buffer = false) {
		// bridge
		$this->buffer = $buffer;
	}

	/** 
	 * (output) returns the parsed and detected components
	 */

	public function output($method = Output::html) {

		// get buffer
		$buffer = $this->buffer;

		// load buffer
		$doc = new \DOMDocument();

		// load the data
		@$doc->loadHTML($buffer);

		// execute query
		$result = $doc->getElementsByTagName("component");	

		// cycle results
		foreach($result as $element) {
			// process attributes
			foreach($element->attributes as $name=>$node) {
				// create
				if($component = $this->create(Compare($name, "name") ? $node->nodeValue : $name)) {
					// replace component
					if($replace = $component->output($method)) {
						// create fragment
						$fragment = $doc->createDocumentFragment();
						// assign fragment
						$fragment->appendXml($replace);
						// replace the node
						$element->parentNode->replaceChild($fragment, $element);
					}
				}
			}
		}

		return $doc->saveHTML();
	}


	/** 
	 * (find)
	 * Finds a view
	 */

	public function find($name, $register = false) {
		// prepare
		$name = strtolower(strpos($name, ".") === false ? "bootstrap." . $name : $name);

		// find components in application and globals
		foreach(array(
			// Application Resources
			ApplicationInfo::location . ComponentsPath::application,
			// Globals
			ComponentsPath::platform
		) as $path) {
			// check if paths exists
			if(is_dir($path . $name)) {
				// return the path
				return $path . $name;
			}
		}	
		// return error
		return false;	
	}


	/** 
	  * (create) creates a new component
	  */

	public function create($name) {
		// find components
		if($path = $this->find($name)) {
			// got the path now load the component
			return new Component($path);
		}

		// not exist
		return false;
	}


	/** 
	  * (has) returns true if a view was loaded
	  */

	public function has($type) {
		
	}
}

