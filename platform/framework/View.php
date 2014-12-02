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


/** 
  * (enum) ViewPath
  */

interface ViewPath {
	const platform = "../global/views/";
	const application = "/views/";
}

/** 
  * (enum) ViewFiles
  */

interface ViewFiles {
	const js = "view.js";
	const php = "view.php";
	const template = "view.template";
	const json = "view.json";
}


/** 
  * (class) View
  * Manages a view
  */

class View extends Primitive {

	/** 
	 * (private)
	*/

	private $configuration = false;
	private $assets = array();

	/** 
	 * (__construct
	 *
	 */

	public function __construct($name = false) {
		// bridge
		if($name) $this->set($name);

	}

	/** 
	 * (output) returns the view output
	 */

	public function output($method = Output::html) {

		// switch by output mode
		switch($method) {

			case Output::html:

				// check
				if($this->has(ViewFiles::template)) {

					// create new template
					$template = new Template($this->assets[ViewFiles::template]);

					// bind data groups
					$template->bind(); 

					// process template
					$buffer = $template->output();

					// process components
					$buffer = $this->__components($buffer);

					// process template
					return $buffer;
				}

				break;
		}

	}

	/** 
	  * (has) returns true if a view was loaded
	  */

	public function has($type) {
		return isset($this->assets[$type]) && $this->assets[$type] !== false;
	}


	/** 
	 * (set) 
	 * sets the view's resources
	 */

	public function set($name) {
		// find the view and register
		return $this->find($name, true);
	}

	/** 
	 * (find)
	 * Finds a view
	 */

	public function find($name, $register = false) {
		// find view in application and globals
		foreach(array(
			// Application Resources
			ApplicationInfo::location . ViewPath::application,
			// Globals
			ViewPath::platform
		) as $path) {
			// check if paths exists
			if(is_dir($path . $name)) {
				// we got a view, register view files
				return $register ? $this->__register($path . $name) : $path . $name;
			}

		}	
		// return error
		return false;	
	}
	

	/** 
	 * (__register)
	 * Registers the view
	 */

	private function __register($path) {
		// reset
		$this->assets = array();

		// load assets
		foreach(array(ViewFiles::template, ViewFiles::php, ViewFiles::js, ViewFiles::json) as $f) {
			// load and assign
			$this->assets[$f] = file_exists($path . "/" .$f) ? file_get_contents($path . "/" . $f) : false;
		}
	}


	/** 
	 * (__components)
	 * Processes the components
	 */

	private function __components($buffer) {
		// create component parser
		$c = new Components($buffer);

		// pass buffer
		return $c->output();
	}

	
}

