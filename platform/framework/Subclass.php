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
  * (class) Subclass
  * Allows to dynamically load an class with the same name
  */

class Subclass {

	/** 
	 * (private)
	*/


	/** 
	 * (__construct) 
	 *
	 */

	public function __construct($class = false, $name = false) {
		
		// get content
		if(file_exists($class)) $class = file_get_contents($class);

		// create classname
		$name = "SubClass" . Guid();
		$namepath = sprintf("\\Booty\\Framework\\%s", $name);

		// prepare object
		$class = trim(str_replace(array("<?php", "?>"), "", str_replace(
			sprintf("class%sextends", Inbetween("class", "extends", $class)),
			sprintf("class %s extends", $name),
			$class
		)));	


		// create object
		$this->__class = eval($class);

		// create subclass
		$this->__class = new $namepath();
	}

	/** 	
	 * default (__get)  
	 * 
     * @param name 		Name of the local variable
	*/

	public function __get($name) {
		// return default
		return isset($this->{$name}) ? $this->{$name} : $this->__retrieve($name, null);
	}

	/** 
	 * default (__set)  
	 * 
     * @param name 		Name of the local variable
     * @param value 	New value of local variable
	*/

	public function __set($name, $value) {
		// set 
		$this->{$name} = $value;
	}

	/** 
	 * default (__call)  
	 * 
     * @param name 		Name of the local variable
     * @param arguments Arguments
	*/

	public function __call($name, $arguments = false) {
		// set default
		return method_exists($this->__class, $name) ? call_user_func_array(array($this->__class, $name), is_array($arguments) ? $arguments : array()) : null;
	}


	/** 
	 * default (__retrieve)  
	 * 
     * @param name 		Name of the local variable
     * @param arguments Arguments
	*/

	public function __retrieve($name, $arguments) {
		return false;
	}

}

