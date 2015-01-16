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


/**
  * Test Processor
  */

$files = array();

array_shift($argv);


// run arguments
foreach($argv as $argument) {

	// prepare switches
	switch($argument) {

		/** (-a) all files */
		case "-a": 

			break;

		/** (default) just add filenames to prceossor */
		default:
			foreach(array($argument, $argument.".php") as $fn) {
				if(file_exists($fn)) {
					$files[] = $fn;
					break;
				}
			}
			break;
	}
}

// Boot up
include("../platform/boot.php");


// Run file processor
foreach($files as $fn) {

	// load file
	include("../test/". $fn);

	// create class name
	$cln = "\Booty\Framework\Test".ucfirst(basename($fn, ".php"));


	// class
	$cl = new $cln();

	if(is_a($cl, "\Booty\Framework\Test")) {

		
		// get methods
		$methods = array_diff(get_class_methods($cl), get_class_methods("\Booty\Framework\Test"), array("Initialize"));

		// cycle through methods
		foreach($methods as $test) {
			try {
				// run test
				if($cl->run($test)) {
					// verify success
					if(!$cl->HasSuccess($test)) break;
				}

			} catch(Exception $e) {
				$cl->Error($test, $e->message);
			}
		}

	} else {

		echo sprintf("Unable to load test for %s.\n", $cln);

	}
}


