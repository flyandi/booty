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
  * (class) Xml
  * Manages an xml document
  */

class Xml extends \SimpleXMLElement {

	/** 
	 * (private)
	*/



	/**
	 * (query)
	 * Executes a query 
	 */
	public function query($exp, $callback = false) {
		// prepare expression
		if(substr($exp, 0, 1) != "/") $exp = "//" . $exp;	
		// execute expression
		$result = $this->xpath($exp);	
		// check result
		if(is_array($result)) {
			// cycle list
			foreach($result as $index => $node) {
				// check callable
				if(is_callable($callback)) {
					$callback($node);
				}
			}
		}
		// return result
		return $result;
	}


	/** 
	 * (asBeautifulXML)
	 */

	public function asBeautifulXML() {
		$dom = dom_import_simplexml($this)->ownerDocument;
		$dom->formatOutput = true;
		return $dom->saveXML();
	}

	/**
	 * (asArray) 
	 */

	public function asArray() {
		$result = array();
		foreach($this as $node) {
			$result[] = (array)$node;
		}
		return $result;
	}

	/**
	 * (asAttributesArray)
	 */

	public function asAttributesArray() {
		$r = array();
		foreach($this->attributes() as $name=>$value) {
			$r[(string) $name] = (string) $value;
		}
		return $r;
	}
}