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

define("BOOTY_RESOURCES_PATH", "../resources/");
define("BOOTY_RESOURCES_APPLICATION", "/resources/");

/** 
  * (enum) ResourcesOutput
  */

 interface ResourcesOutput {
 	const raw = 0;
 	const http = 1;
 	const httpdownload = 2;
 }

/** 
  * (class) Resources
  * This is the primary object
  */

class Resources extends Primitive {

	/** 
	 * (privates)
	 */

	private $buffer;
	private $configuration;

	private $list = false;


	/** 
	 * (__construct) 
	 *
     * @param source 		Specify the source of the configration 
	*/
	public function __construct($configuration = false) {
		// create collection
		$this->list = new Collection();

		// implement config
		if($configuration) $this->configuration = $configuration;
	}


	/** 
	 * (register) 
	 * Registers a resource
	 *
	 * @param value
	 */

	public function register($resources, $params = false) {

		// prepare
		if(!is_array($resources)) $resources = array($resources);

		// initialize
		$that = $this;

		// traverse array
		TraverseArray($resources, function($key, $value) use ($that) {
			// add resource
			$that->add($value);
		});
	}


	/** 
	 * (add) 
	 * Adds a resource
	 *
	 * @param name
	 */

	public function add($values) {
		// internal fields
		if(is_string($values) && substr($values, 0, 1) == "@") {
			// loadable resources
			global $BOOTY_GLOBAL;

			// cycle
			foreach($BOOTY_GLOBAL->resources as $key=>$value) {
				if(Compare($key, $values)) {
					$values = $value;
					break;
				}
			}
		}

		// eval
		switch(true) {
			case is_array($values):
				// cycle each value
				foreach($values as $value) $this->add($value);
				break;

			case is_dir($values):
				echo "DIR";

				break;
		}
	}

	/** 
	 * (emit) 
	 * Emits the resource
	 *
	 * @param value
	*/

	static public function emit($source, $output = ResourcesOutput::http, $headers = false) {
		// check if source exists
		if(file_exists($source)) {
			// check output type
			switch($output) {
				// (http)
				case ResourcesOutput::http:
					HTTP::Output($source);
					break;

				// (httpdownload)
				case ResourcesOutput::httpdownload:

					HTTP::Download($source);
					break;

				// (raw)
				default:
					// output
					readfile($source);
					break;
			}
		}
	}


	/** 
	 * (handleRequest) 
	 * Handles the current request 
	 *
	 * @param request
	*/

	static public function handleRequest($source, $request = false, $strict = false) {

		// prepare request
		$request = DefaultValue($request, GetRequest(1));

		// switch resource requester
		switch(true) {

			/** Passthrough */
			default:
				// create filter reader
				$filer = new Files();
				// find resource
				foreach(array(
					// Internal Resources
					BOOTY_RESOURCES_PATH, 
					// Application Resources
					$source ? $source . BOOTY_RESOURCES_APPLICATION : false,
				) as $path) {
					// get list
					$list = new Collection($filer->get($path, FilesFilters::onlyfiles, true));

					// find list
					if($match = $list->cycle(function($key, $value) use ($path, $request, $strict) {
						// prepare
						$stripped = "/" . str_replace($path, "", $value);
						// verify path
						if(substr(dirname($stripped), 0, strlen(dirname($request))) == dirname($request)) {
							// check filename
							//if($stripped == )
							if($stripped == $request || (!$strict && Compare(basename($stripped), basename($request)))) {
								// found resource
								return $value;
							}
						}

					})) {
						// emit resource
						Resources::Emit($match);
						break;
					}
				}
				break;
		}
	}
}