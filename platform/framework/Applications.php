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

// Application Paths
define("BOOTY_APPLICATION_PATH", "../applications/");
define("BOOTY_APPLICATION_DEFAULTFILE", "Application.php");
define("BOOTY_APPLICATION_DEFAULTCLASS", "Application");


/** 
  * (class) Configuration
  * This is the primary object
  */

class Applications extends Primitive {

	/** 
	 * (public)
	*/

	public $application = false;

	/** 
	 * (__construct) 
     * @param source 	Specify the source of the configration 
     * @param format 	Format of the source, otherwise autodetect
	*/

	public function __construct() {

	}


	/** 
	 * (has) matches an application
     * @param map 		Detects based on a map if supplied
	*/

	public function has() {
		// handle is the required function for an application to work
		return method_exists($this->application, "handle");
	}

	/** 
	 * (detect) matches an application
     * @param map 		Detects based on a map if supplied
	*/

	public function detect($map) {
		// check if map is valid
		if(!is_array($map) || count($map) == 0) {
			// get map
			$map = $this->__getautoload();
		}

		// check if we have a map
		if(is_array($map)) {

			// cycle map 
			foreach($map as $application) {
				// initialize
				$detected = false;
				// item
				$item  = $application->_;
				// run match filters
				foreach(array(
					"host"=>function($b) {
						return fnmatch($b, GetServerVar("HTTP_HOST"));
					}
				) as $key=>$fn) {
					if(isset($item[$key])) {
						// initialize
						$d = false;
						// check array
						$item[$key] = !is_array($item[$key]) ? array($item[$key]) : $item[$key];
						// cycle values
						foreach($item[$key] as $value) {
							if(!$d) $d = $fn($value);
						}
						// assign
						$detected = $d;
					}
				}

				// check detected
				if($detected) {
					// found an matching application
					$this->__load($application);
				}

			}
		}
	}
	

	/** 
	 * (__load) loads an application
	 * @param application 			An application container or ID
     * 
	*/

	private function __load($application) {
		// check id
		if(is_string($application)) {
			// missing implementation
		}

		// load configuration
		$configuration = new Configuration($application->path . "/" . ConfigurationFiles::application);

		// create filename
		$filename = $application->path . "/" . DefaultValue(@$configuration->application->_applicationfile, BOOTY_APPLICATION_DEFAULTFILE);
		
		// load filename
		if(file_exists($filename)) {
			// include
			require_once($filename);

			// create application constants
			foreach(array(
				"location" => dirname($filename),
				"filename" => $filename,
			) as $k=>$v) {
				define("_APPLICATION_" . strtoupper($k), $v);
			}

			// create class
			$this->application = new \Application\Application(
				// origin
				dirname($filename),
				// configuration
				$configuration
			);

			
		}
	}


	/** 
	 * (__getautoload) returns a map from all applications autoload section
     * 
	*/

	private function __getautoload() {
		// initialize
		$map = array();
		// get files
		$file = new Files();
		// discover objects
		foreach($file->get(BOOTY_APPLICATION_PATH) as $path) {

			// get app configuration
			$application = new Configuration($path . "/" . ConfigurationFiles::application);

			// check app
			if($application->id && $application->enabled) {
				// build map
				$map[] = (object) array(
					"id" => $application->id,
					"path" => $path,
					"_" => $application->asArray("autoload")
				);
			}
		}
		// return
		return $map;
	}


}

