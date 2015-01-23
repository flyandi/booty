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
  * Simple Test Framework 
  */

class Test {

	/**
	  * Const
	  */

	const skip = "@skip";

	/**
	  * Map
	  */

	private $fails = array();

	/** 
	  * Constructor
	  */

	public function __construct() {

		// initialize method
		if(method_exists($this, "Initialize")) $this->Initialize();

	}

	/**
	  * Asserts
	  */

	public function AssertTrue($condition, $notice = false) {

		if(!$condition) $this->Fail("Assertion failed (AssertTrue)", $notice);
	}

	/**
	  * Run
	  */

	public function Run($test) {

		if(method_exists($this, $test)) {

			// run test
			$result = $this->$test();

			// check
			return $result != Test::skip;
		}

		return false;
	}


	/**
	  * Fail
	  */

	public function Fail($message, $notice = false) {

		$trace = false; $x = 1;
		while(!$trace) {
			$trace = debug_backtrace();
			$trace = (object) $trace[count($trace)-$x];

			if($trace->function == "Run") {
				$x++;
				$trace = false;
			}

		}

		$this->fails[$trace->function] = true;

		$this->Report(sprintf("%s: [Failed] %s%s", $trace->function, $message, $notice ? ": ".$notice : ""));

	}

	/**
	  * HasSuccess
	  */

	public function HasSuccess($test) {
		if(!isset($this->fails[$test])) {
			$this->Report(sprintf("%s: [Passed]", $test));
			return true;
		}
		return false;
	}

	/**
	  * HadSuccess
	  */

	public function HadSuccess($test) {
		return !isset($this->fails[$test]);
	}

	/**
	  * Error
	  */

	public function Error($test, $message) {
		$this->Report(sprintf("%s: [Error] %s", $test, $message));
	}


	/**
	  * Report
	  */

	public function Report($message) {
		switch(true) {

			case php_sapi_name() == 'cli':
				echo $message."\n";

				break;
		}
	}
}