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
  * (class) HTTP
  */

class HTTP extends Primitive {


	/**
	 * (HTTP Versions) 
	 */

	const version10 = "1.0";
	const version11 = "1.1";

	/**
	 * (HTTP Statuses)
	 */

	const http200 = "200 OK";
	const http404 = "404 Not Found";
	const http500 = "500 Internal Server Error";
	const http501 = "501 Not Implemented";
	const http260 = "260 API";


	/** 
	 * (__construct) 
	 *
	*/

	public function __construct() {


	}

	/** 
	 * (Status) sets the http status of this envelope
	 * 
     * @param headers 		Array of headers
	*/
	static public function Status($status, $version = HTTP::version11) {
		// set header status
		header(sprintf("HTTP/%s %s", $version, $status));
	}

	/** 
	 * (Header) creates the headers
	 * 
     * @param headers 		Array of headers
	*/

	static public function Header($headers, $status = HTTP::http200, $version = HTTP::version11) {
		// set status
		self::Status($status, $version);

		// build header
		foreach(is_array($headers) ? $headers : array() as $name=>$value) {
			header(sprintf("%s: %s", $name, $value));
		}
	}

	/**
	  * (HeadersNoCache) returns default no cache headers
	  */
	static public function HeadersNoCache() {
		return array(
			"Expires" => date(DATE_RFC822, time() - (3600 * 24 * 365)),
			"Last-Modified" => date(DATE_RFC822, time() - 120),
			"Pragma" => "no-cache",
			"Cache-Control" => "no-store, no-cache, must-revalidate",
			"Cache-Control" => "post-check=0, pre-check=0",
		);
	}

	/**
	  * (Output) outputs anything
	  *
	  * @param source 			Any source
	  * @param mime 			The HTTP mime type
	  */

	static public function Output($buffer, $mime = FilesMimeType::plain, $compress = false, $headers = false, $terminate = true, $status = HTTP::http200, $version = HTTP::version11) {
		// get headers
		if(!$headers || !is_array($headers)) $headers = Self::HeadersNoCache();

		// check if this is file
		switch(true) {

			// (string)
			case is_string($buffer):

				// check if file
				if(is_file($buffer)) {
					// get file info
					$mime = Files::Mime($buffer, $mime);

					// read source
					$buffer = file_get_contents($buffer);
				}

				break;

		}

		// output source
		if($buffer) {
			// compress
			if($compress) {
				// handle compression
				if($ae = ServerVar("HTTP_ACCEPT_ENCODING", false)) {
					// initialize
					$encoding = false;	
					// retrieve proper enconding
					if(strpos($ae, 'x-gzip') !== false ){
						$encoding = 'x-gzip';
					} elseif( strpos($ae,'gzip') !== false ){
						$encoding = 'gzip';
					}
					// sanity check
					if($encoding) {
						// encode
						$buffer =gzencode($buffer, 6);
						// add to headers
						$headers['Content-Encoding'] = $encoding;
					}
				}
			}

			// adjust header
			//$headers['Content-Length'] = is_string($buffer) ? strlen($buffer) : sizeof($buffer);
			$headers['Content-Type'] = $mime;

			// emit headers
			self::Header($headers, $status, $version);

			// emit content
			echo $buffer;

			// terminate
			if($terminate) exit;	

			// return
			return true;
		}

		return false;
	}

}

