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
  * (enum) TemplateFields 
  */

interface TemplateFields {
	const macro = "#";
	const callback = "&";
	const translate = "!";

}

interface TemplateDataSource {
	const json = "::json";
	const php = "::php";
	const db = "::db";
}


/** 
  * (class) Template
  * Manages a templated document
  */

class Template extends Primitive {

	/** 
	 * (private)
	*/

	private $configuration = false;
	private $content = false;

	/** 
	 * (__construct) 
	 *
	 */

	public function __construct($content = false) {
		// cler
		$this->clear();

		// check filename
		if($content) $this->apply($content);

	}

	/** 
	 * (apply) applies a resource to it
	 */

	public function apply($content) {
		// check true
		switch(true) {
			// filename
			case strlen($content) < 512 && is_file($content):
				$this->content = file_get_contents($content);
				break;

			// default
			default:
				$this->content = $content;
		}
	}

	/** 
	 * (add) applies a value to the template
	 */

	public function add($name, $value = null) {
		// verify
		if(!is_array($name)) $name = array($name => $value);
		// add to values
		foreach($name as $key=>$value) {
			if($value !== null) {
				$this->values[strtolower($key)] = $value;
			}
		}
	}

	/**
	 * (remove) removes a value 
	 */

	public function remove($name) {
		if(isset($this->values[$name])) {
			unset($this->values[$name]);
		}
	}

	/** 
	 * (clear) removes all values
	 */

	public function clear() {
		$this->values = array();
	}


	/** 
	  * (has) 
	  */

	public function has($name) {
		return isset($this->values[$name]);;
	}


	/** 
	 * (bind) binds default resources 
	 */

	public function bind() {
		// add request data
		$this->add($_REQUEST);
	}


	/** 
	  * (output) 
	  */

	public function output() {
		// process document
		return $this->__process();
	}


	/** 
	  * (__processfields) 
	  */

	private function __process() {
		// get buffer
		$buffer = $this->content;

		// process fields
		$buffer = $this->__processfields($buffer);

		// return
		return $buffer;
	}


	/** 
	  * (__processfields) 
	  */

	private function __processfields($buffer = false) {
		// generate buffer
		if($buffer === false) $buffer = $this->content;

		// get fields
		$fields = $this->__getfields();

		// check and process
		if(is_array($fields)) {
			//cycle
			foreach($fields as $field) {
				// initialize
				$replace = null;

				// get field parameters
				$parts = explode(".", $field);

				// pre formatting
				$params = (object) array(
					"ident" => substr($field, 0, 1),
					"field" => $parts[0],
					"name" => substr($parts[0], 1),
					"params" => count($parts) > 1 ? explode(",", str_replace(array("(", ")"), "", $parts[1])) : false
				);

				// switch by identifier 
				switch($params->ident) {

					/**
					  * (Macro) 
					  * Allows the interaction with the PHP world
					  */

					case TemplateFields::macro:

						// check if the macro is valid
						if(function_exists($params->name)) {
							// get value
							$replace = forward_static_call_array($params->name, $params->params);
						}
						break;

					/** 
					  * (Default)
					  * Grap from values
					  */

					default:
						if(isset($this->values[strtolower($params->field)])) {
							$replace = $this->values[strtolower($params->field)];
						}
						break;
				}

				// check
				$buffer = str_replace(VARIABLE_FIELD_BEGIN . $field . VARIABLE_FIELD_END, $replace !== null ? $replace : "", $buffer);
			}
		}

		// return buffer
		return $buffer;
	}


	/** 
	  * (__getfields)
	  */

	private function __getfields() {
		// initialize buffer
		$buffer = $this->content;

		// remove all references to scripts or styles
		foreach(array("scripts", "style") as $tag) {
			// remove 
			$buffer = preg_replace(sprintf("#<%s[^>]*>.*?</%s>#is", $tag, $tag), "", $buffer);
		}

		// run field detection
		if(preg_match_all(sprintf("/%s(.*?)%s/", VARIABLE_FIELD_BEGIN, VARIABLE_FIELD_END), $buffer, $matches, PREG_PATTERN_ORDER)) {
			// return result
			return $matches[1];
		}

		// return error
		return false;
	}
}

