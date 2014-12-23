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


interface ApiStatus {
	const ok = 1;
	const error = 2;
	const autherror = 3;
	const authrequired = 4;
	const notfound = 5;
	const notsupported = 6;
	const mismatch = 7;
}


/** 
  * (class) Api
  * Manages API calls. This is the only class that uses purely static access methods
  */

class Api extends Primitive {

	/** 
	 * (private)
	*/

	private $data = false;

	
	/** 
	 * (reset) 
	 *
	 */

	public function reset() {
		$this->data = (object) array(
			// status
			"status" => ApiStatus::notsupported
		);
	}
		

	/** 
	 * (prepare) 
	 *
	 */

	public function prepare($status = ApiStatus::notsupported, $data = false) {

		// reset
		$this->data = (object) Extend(array(
			// status
			"status" => ApiStatus::notsupported,

		), !is_array($data) || !is_object($data) ? array("data" => $data) : (array) $data);

		// set status
		$this->status($status);
	}


	/** 
	  * (status)
	  */

	public function status($status = null) {	

		if($status != null) $this->data->status = $status;

		return $this->data->status;
	}


	/**
	  * (has)
	  */

	public function has() {
		return is_object($this->data);
	}


	/** 
	 * (output) returns the parsed and detected components
	 */

	public function emit() {

		// prepare data
		$data = (object) Extend(array(
			"status" => ApiStatus::notsupported,
		), (array) $this->data);


		// output
		HTTP::output(
			// payload
			json_encode((array) $data), 
			// type
			FilesMimeType::json,
			// compress
			false,
			// headers
			HTTP::HeadersNoCache(), 
			// terminate
			true, 
			// status code
			(260 + ($data->status)) . " API " . strtoupper(ReverseConstant(__NAMESPACE__ . "\ApiResult", $data->status))
		);
	}
}

