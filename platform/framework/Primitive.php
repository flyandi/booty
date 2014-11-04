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
  * (Primitive) Base class for all other classes
  */
class Primitive {

	/**
	  * (privates)
	  */

	private $implements = array();


	/** 
	 * default (config)  
	 * 
     * @param name 		Name of the global configuration key
     * @param default 	A default value
	*/

	public function config($name, $default = null) {
		global $BOOTY_GLOBAL;

		return isset($BOOTY_GLOBAL->{$name}) ? $BOOTY_GLOBAL->{$name} : $default;

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

	public function __call($name, $arguments) {
		// set default
		return count($arguments) != 0 ? $this->__set($name, $arguments[0]) : $this->__get($name);
	}

	/** 
	 * (implement)  
	 * 
     * @param 
     * 
     */

	public function implement($reference) {

		// implements
		$this->implements[] = $reference;
	}


	/** 
	 * (__retrieve)  
	 * 
     * @param 
     * 
     */

	public function __retrieve($name, $default) {
		foreach($this->implements as $class) {
			if(isset($class->{$name})) return $class->{$name};
		}
		return $default;
	}


}
