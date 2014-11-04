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


// Configuration patterns and locations
define("BOOTY_CONFIGURATION_GLOBAL", "../configuration/Booty.json");
define("BOOTY_CONFIGURATION_APPLICATION", "Application.json");

// Configuration formats
define("BOOTY_CONFIGURATION_JSON", 0);
define("BOOTY_CONFIGURATION_XML", 1);



/** 
  * (class) Configuration
  * This is the primary object
  */

class Configuration {

	/** 
	 * (privates)
	 */

	private $buffer;

	public $definitions; 

	/** 
	 * (__construct) 
	 *
     * @param source 		Specify the source of the configration 
     * @param format 		Format of the source, otherwise autodetect
     * @param autoregister 	Auto register variables
	*/
	public function __construct($source = false, $format = false) {
		// initialize definitions
		$this->definitions = (object) array();

		// try to load source
		if($source) $this->__load($source, $format);

	}

	/** 
	 * (get) returns a configuration key based on a string path 
	 *
     * @param path 			The path
     * @param default 		The default value returned
    */
    
    public function get($path, $default = null) {

    	return $default;
    }

	/** 
	 * (__get) returns the configuration key
	 * 
     * @param name 		Name of the configuration key
	*/
	public function __get($name) {
		// prepare
		$name = strtolower($name);
		$value = isset($this->buffer->{$name}) ? $this->buffer->{$name} : (isset($this->definitions->{$name}) ? $this->definitions->{$name} : null);
		// validate
		if($value !== null) {
			// return the value
			return is_array($value) ? (object) $value : $value;
		}
		// return default
		return null;
	}

	/** 
	  * (__call) returns the configuration key as type
	  * 
	  * @param name 	Name of the configuration key
	  */
	public function __call($type, $params) {
		// check parameters
		if(isset($params[0])) {
			// get value
			$value = $this->{$params[0]};
			// check value
			if($value !== null) {
				// switch by type
				switch(strtolower($type)) {
					// (asArray)
					case "asarray": 
						return is_object($value) ? (array) $value : (!is_array($value) ? array($value) : $value);
				}
			}
		}

		// return default
		return null;
	}


	/** 
	 * (__load) Loads the specific source into the buffer
	 * 
     * @param source 	Specify the source of the configration 
     * @param format 	Format of the source, otherwise autodetect
	*/
	private function __load($source, $format = false) {

		// switch by true
		switch(true) {

			// (is:filename)
			case file_exists($source):

				// load buffer and decode
				if(!$format) {
					// get by file extension map
					$map = array(
						"json"=>BOOTY_CONFIGURATION_JSON,
						"xml"=>BOOTY_CONFIGURATION_XML
					);
					// get extension
					$ext = pathinfo($source, PATHINFO_EXTENSION);
					// assign by map
					$format = in_array($ext, $map) ? $map[$ext] : false;
				}

				// check format
				if($format !== false) {

					// load source
					$source = StripWhitespace(file_get_contents($source), true, true);

					// load by format
					switch($format) {
						//(json)
						case BOOTY_CONFIGURATION_JSON:
							$this->buffer = json_decode($source);
							break;

						case BOOTY_CONFIGURATION_XML:
							// Need to be implemented
							break; 
					}

					// register
					$this->register($this->buffer);


					// loaded
					return true;
				}

				break;

		}
		// not possible to load this source
		return false;

	}

	/** 
	  * (register) register variables
	  * 
	  * @param node 			The node
	  * @param recursive 	    Scans the entire node for registers
	  */
	public function register($node, $recursive = true, $parent = false) {
		// scan through nodes
		if(is_array($node) || is_object($node)) {
			foreach((array)$node as $name=>$children) {
				
				// validate
				switch(true) {
					// Definitions 
					case fnmatch("@define*", $name):
						// initialize
						$map = array($name);
						// check if there is an index set
						if(isset($children->{"@index"})) {
							// found an index
							$map = $children->{"@index"};
							// remove index
							unset($children->{"@index"});
						} 
						// cycle children
						foreach($children as $key=>$value) {
							// process map
							foreach($map as $index=>$expression) {
								// evaluate expression
								$pos = explode("(", $expression);
								$fnc = $pos[0];
								$arg = count($pos) > 1 ? explode(",", substr($pos[1], 0, strpos($pos[1], ")"))) : false;

								// switch true
								switch(true) {
									// global constants
									case Compare("@define", $fnc):	
										// prepare key 
										$key = strtoupper(isset($arg[0]) ? $arg[0].$key : $key);

										// register constant
										if(!defined($key))
											define($key, $value[$index]);

										break;
									// bind constants to definitions
									default:
										// register parent
										if(!isset($this->definitions->{$parent})) $this->definitions->{$parent} = (object)array();
										// register key
										$this->definitions->{$parent}->{($arg[0] !== null && $arg[0] !== "" ? $value[$arg[0]] : $key)} = DefaultValue(@$value[$index], null);
										break;
								}
							}
					
						}
						break;

					case $recursive && (is_object($children) || is_array($children)): 
						// register
						$this->register($children, $recursive, $name);
						break;
				}
			}
		}
	
	}

}

