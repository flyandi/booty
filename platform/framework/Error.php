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
  * (class) Error
  * Booty's Error Management
  */

class Error extends Primitive {

	/** 
	 * (private)
	 */
	private $error;


	/** 
	 * (__construct) 
	 *
     * @param error 	An error number 
	*/
	public function __construct($error = false) {
		// assign
		$this->error = $error;
		if($error) $this->__load($source, $format);

	}


	/** 
	 * (descriptions) returns a description
	 *
     * @param error 	An error number 
	*/

	static public function description($error) {
		// globals
		global $BOOTY_GLOBAL;

		// get description for this erro
		return DefaultValue($BOOTY_GLOBAL->definitions->error->{$error}, null);
	}

    /** 
	 * (handle) handles an error
	 *
     * @param error 	An error number 
	*/

	static public function handle($error, $description = false) {
		// globals
		global $BOOTY_GLOBAL;

		// check error configuration
		self::message(
			DefaultValue(self::description($error), ""), 
			FillVariableString(DefaultValue($description ? $description : @$BOOTY_GLOBAL->error->description, ""), array($error))
		);

	}

 	/** 
	 * (message) displays an message
	 *
     * @param error 	An error number 
	*/
	static public function message($message = false, $description = false, $code = false, $httpcode = HTTP::http501) {
		// globals
		global $BOOTY_GLOBAL;

		// create variables
		$variables = array("message"=>$message, "code"=>$code, "description"=>$description);

		// create message
		if(defined("BOOTY_CONSOLE")) {
			// we are in console mode, display basic error
			die(sprintf("\nError (%s): %s\n%s\n", $code, $message, $description));
		}

		// create http header
		HTTP::Header(HTTP::HeadersNoCache(), $httpcode); 

		// create message
		Die(sprintf("<DOCTYPE html><head><title>%s</title><head%s</head>%s</html",
			// title
			FillVariableString(DefaultValue($BOOTY_GLOBAL->error->html->title, "{code}"), $variables),
			// header
			implode("\n", array(
				Tag("meta", array("name"=>"robot", "value"=>"noindex"), false, true)
			)),
			// body
			Tag("body", array("style"=>sprintf("width:100%%;height:100%%;overflow:hidden;background:url(%s) no-repeat center 50px #f1f1f1", DefaultValue($BOOTY_GLOBAL->assets->logo, ""))), 
				sprintf("%s%s",
					Tag("div", Array("style"=>"text-shadow:#fff 1px 1px 0px;color:#333;font:bold 2em Arial;text-align:center;margin-top:270px;letter-spacing:-1px;"), $message),
					Tag("div", Array("style"=>"padding-top:30px;text-align:center;font:normal 1.2em Arial;color:#555;text-shadow:#fff 1px 1px 0px;"), $description)
				)
			)
		));
	}

	
}

