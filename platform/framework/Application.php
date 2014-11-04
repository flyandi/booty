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
  * (enum)
  */

interface Context {
	const anonymous = 0;
	const user = -1;
	const system = -3;
	const cron = -4;
	const console = -5;
}

interface Output {
	const html = 0;
	const json = 1;
	const xml = 2;
	const console = 3;
}

/** 
  * (class) Application
  * This is the primary Application controller
  */

class Application extends Primitive {

	/** 
	 * (private)
	 */

	private $configuration = false;
	private $output = Output::html;
	private $headers = array();

	/** 
	 * (__construct) 
	 *
	 */

	public function __construct($location, $configuration) {

		// apply
		$this->location = $location;
		$this->configuration = $configuration;

		// resources, hold the current resources
		$this->resources = new Resources($this->configuration->resources);

		// view, holds the current representation
		$this->view = new View();

		// routes, manages routes
		$this->routes = new Routes();

		// run registration
		$this->__register();

	}

	/** 
	 * (__call)  
	 * Loads dynamically an 
	 *
	 * @param name 				Name of the view
	 */

	public function __call($name, $arguments) {

	}


	/** 
	 * (header)  
	 * sets an header
	 *
	 * @param name 				Name of the header
	 * @param value 			Value 
	 */

	public function header($name, $value = false) {
		// create array
		if(!is_array($name)) $name = array($name => $value);
		// assign to headers
		$this->headers = array_merge($this->headers, $name);
	}


	/** 
	 * (emit)  
	 * This is the primary output function
	 *
	 */

	public function emit($output = Output::html) {

		// set output
		$this->output = $output;

		// process routes
		$this->__handleRoutes();

		// process output
		$this->__handleOutput();


	}

	/** 
	 * (context)  
	 * Returns the application context
	 *
	 */

	public function context() {

		return Context::anonymous;

	}


	/** 
	 * (__register)  
	 * Registers the configuration
	 *
	 */

	private function __register() {

		// resources
		$this->resources->register(DefaultValue(@$this->configuration->resources, false));

	}

	/** 
	 * (__handleRoutes)  
	 * This is the primary routing function
	 *
	 */

	private function __handleRoutes() {
		// get globals
		global $BOOTY_GLOBAL;

		// process default routes
		switch($request = GetDirVar(0, BOOTY_ROUTE_ROOT)) {

			// (Resources)
			case BOOTY_ROUTE_RESOURCES: 
				// let resources handle this request
				Resources::handleRequest($this->location, $this->context());
				break;


			// (Process other)
			default:

				// nothing else is defined, pass to applicaton
				if(method_exists($this, "handle")) {
					// handle request
					$this->handle($request, $this->context());

				} else {
					// fail since no endpoint has been defined
					Error::handle(BOOTY_ERROR_NOENDPOINT);
				}

				break;
		}

	}

	/** 
	 * (__handleOutput)  
	 * This is the primary output function
	 *
	 */

	private function __handleOutput() {

		// check view
		switch($this->output) {

			// html
			case Output::html:

				// build header
				HTTP::Header($this->headers);

				// build document
				$document = sprintf("<!DOCTYPE %s><head>%s</head><body booty>%s</body></html>",
					// doctype
					$this->configuration->get("document/doctype", "html"),
					// build header
					sprintf("%s", 
						// Resources
						$this->resources->compile(
							// check how to compile the resources
							$this->configuration->get("application/resources_inclusion", false)
						)
					),

					// build output
					$this->view->output($this->output)
				);

				// run filters


				// output document
				echo $document;

				// terminate
				exit;

				break;

			// json
			case Output::json:

				break;


		}


	}
}

