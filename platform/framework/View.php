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
	const js = ".js";
	const php = ".php";
	const template = ".template";
	const json = ".json";
	const _default = "default";
}

interface ViewRoutes {
	const _default = "default";
	const action = "action";

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
	private $name = false;
	private $action = false;

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


			/** 
			  * API Output
			  */

			case Output::api:

				// reset api session
				Api::instance()->reset();


				switch(true) {

					/** 
					  * (Action)
					  */

					case GetVar(ViewRoutes::action, false) !== false:

						

						// process the external library
						$result = $this->__process(GetVar(ViewRoutes::action, false));

						// validate result
						if(is_object($result)) {	

							// return api content
							return Api::instance()->prepare(ApiStatus::ok, (array) $result);

						}

						break;


					default:
						// requires a javascript file
						if($this->has(ViewFiles::js)) {

							return Api::instance()->prepare(ApiStatus::ok, Extend(array(

								// type
								"type" => "View",
							
								// prepare source
								"source" => PrepareScript($this->asset(ViewFiles::js)),

							), json_decode($this->asset(ViewFiles::json))));

						}

						break;
				}

				return Api::instance()->status(ApiStatus::error);


				break;
			/**
			  * HTML Output
			  */

			case Output::html:
				// check javascript
				switch(true) {
					// check javascript
					case $this->has(ViewFiles::js):

						// register tag
						return HTML::Tag("script", array(
							"type" => "View",
							"source" => PrepareScript($this->asset(ViewFiles::js)),
							"invoke" => HTMLValues::true,
						));

						break;


					// check template
					case $this->has(ViewFiles::template):

						// create new template
						$template = new Template($this->asset(ViewFiles::template));

						// bind data groups
						$template->bind(); 

						// process template
						$buffer = $template->output();

						// process components
						$buffer = $this->__components($buffer);

						// process template
						return $buffer;
						
						break;

				}

				break;
		}

	}

	/** 
	  * (assets) returns the actual assets
	  */

	public function asset($type, $default = false) {

		// check
		foreach(array($this->action, $this->name, ViewRoutes::_default) as $name) {

			// asset filename
			$fn = $name.$type;

			if(isset($this->assets[$fn]) && file_exists($this->assets[$fn])) {
				return file_get_contents($this->assets[$fn]);
			}
		}

		return $default;
	}

	/** 
	  * (has) returns true if a view was loaded
	  */

	public function has($type) {

		return $this->assets($type) !== false;
	}


	/**
	  * (route) initializes the view based on routes
	  */

	public function route($default = ViewRoutes::_default) {
		// set from route
		$this->set(GetDirVar(0, $default), GetDirVar(1, false));
	}


	/** 
	 * (set) 
	 * sets the view's resources
	 */

	public function set($name, $action = false) {
		// find the view and register
		if($this->find($name)) {
			$this->name = $name;
			$this->action = $action;
		}
	}

	/** 
	 * (find)
	 * Finds a view
	 */

	public function find($name) {

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
				return $this->__register($path . $name);
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

		// read path
		$filer = new Files();

		// clear
		$this->assets = array();

		// assign assets
		foreach($filer->get($path, FilesFilters::onlyfiles) as $path) {
			$this->assets[basename($path)] = $path;
		}

		return count($this->assets) != 0;
	}


	/** 
	 * (__components)
	 * Processes the components
	 */

	private function __components($buffer) {
		// create component parser
		$c = new Components($buffer);

		// pass buffer
		return $c->parse();
	}

	/**
	 * (__process)
	 * Processes the outside script
	 */

	private function __process($action = false, $output = false) {

		if($this->has(ViewFiles::php)) {

			// prepare 
			$instance = new Subclass($this->asset(ViewFiles::php));

			// return
			return $instance ? $instance->{$action}((object)GetRawVar()) : false;

		}
		
		return false;

		
	}

	
}

