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
  * Working Directory
  */

chdir(dirname(__FILE__));

/**
  * (spl_autoload_register)
  */
spl_autoload_register(function($className) {

	if($className[0] == '\\') {
		$className = substr($className, 1);
	}

	// initialize
	$classPath = false;

	// check handler
	switch(true) {

		// Framework Namespace
		case stripos($className, "Booty\\View") !== false:
		case stripos($className, "Booty\\Framework") !== false:
			$classPath = lcfirst(strtr(substr($className, strpos($className, "Booty\\") + 6), "\\", "/")) . ".php";
			break;

		default:
			return;
	}

	// include
	if($classPath) {
		// prepare 
		$classPath = __DIR__ . "/" . $classPath;
		// sanity check
		if(file_exists($classPath)) require_once($classPath);
	}
});


/**
  * (Global Includes)
  */

foreach(array("Helpers") as $c) {
	require_once("library/" . $c . ".php");
}


/**
  * (Global Configuration)
  */

SetVar("BOOTY_GLOBAL", $BOOTY_GLOBAL = new Booty\Framework\Configuration(Booty\Framework\ConfigurationFiles::globals));
