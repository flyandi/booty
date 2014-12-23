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


interface ScriptWrapper {

	const html = "../globals/scripts/output/html.js";

}

interface ScriptTypes {

	const view = "view";
}


/** 
  * (class) Script
  * Manages a script to NodeJS
  */

class Script extends Primitive {

	/** 
	 * (private)
	*/


	/** 
	 * (__construct) 
	 *
	 */

	public function __construct($content = false) {
		// cler
		$this->content = $content;

	}


	/**
	 * (output)
	 */

	public function output($class = false, $data = false, $method = Output::html) {
		// get buffer
		$buffer = $this->content;

		// switch by method
		switch($method) {

			// html
			case Output::html: 
				// append html output routine
				$buffer .= $this->__load($class, $data, ScriptWrapper::html);

				// pass to nodejs
				if($result = $this->__execute($buffer)) {

					return $result;

				}

				break;

		}

		// return default
		return false;
	}


	/**
	 * (__execute)
	 */

	private function __execute($buffer) {

		$buffer = escapeshellarg(trim(StripWhitespace($buffer, true, true)));

		// quick implementation
		$result = shell_exec(sprintf("node -e %s", trim($buffer)));

		// check result
		return $result; // or not
	}


	/**
	 * (__load)
	 */

	private function __load($class, $data, $wrapper) {
		// load wrapper
		$buffer = file_get_contents($wrapper);

		// replace class
		$buffer = str_replace(VARIABLE_FIELD_BEGIN . "class" .VARIABLE_FIELD_END, $class, $buffer);

		// replace data
		$buffer = str_replace(VARIABLE_FIELD_BEGIN . "data" . VARIABLE_FIELD_END, json_encode($data), $buffer);
		
		// return buffer
		return $buffer;
	}
}

