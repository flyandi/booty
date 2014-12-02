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


interface ComponentFiles {
	const js = "component.js";
	const json = "component.json";
	const template = "component.template";
}


/** 
  * (class) View
  * Manages a view
  */

class Component extends Primitive {

	/** 
	 * (private)
	*/


	/** 
	 * (__construct) 
	 *
	 */

	public function __construct($location) {

		// bridge
		$this->location = $location;

		// register
		$this->__register($location);
	}


	/** 
	 * (output) returns the parsed and detected components
	 */

	public function output($method = Output::html) {

		// prepare script
		$script = $this->has(ComponentFiles::js) ? new Script($this->assets[ComponentFiles::js]) : false;


		// switch by output mode
		switch($method) {

			case Output::html:

				// safety check
				if($script) {
					return $script->output("Component", $method);
				}

				break;
		}

		// return false
		return false;
	}


	/** 
	  * (has) returns true if a view was loaded
	  */

	public function has($type) {
		return isset($this->assets[$type]) && $this->assets[$type] !== false;
	}


	/** 
	 * (__register)
	 * Registers the view
	 */

	private function __register($path) {
		// reset
		$this->assets = array();

		// load assets
		foreach(array(ComponentFiles::template, ComponentFiles::js, ComponentFiles::json) as $f) {
			// load and assign
			$this->assets[$f] = file_exists($path . "/" .$f) ? file_get_contents($path . "/" . $f) : false;
		}
	}
}

