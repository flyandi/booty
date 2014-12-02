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
  * (enum)
  */

interface CollectionQuery {
	const all = true;
}


/** 
  * (class) CollectionItem
  * Helper class for shadow data
  */

class CollectionItem extends Primitive {

	/** 
	 * (__construct) 
	 *
     * @param name 		The name
     * @param data 		Any data
	*/

	public function __construct($name, $data = null, $meta = null) {
		// assign name
		$this->name = $name;
		// prepare data
		if($data !== null && !is_array($data)) $data = array($data);
		// assign
		foreach(array_merge(
			// data bag
			is_array($data) ? $data : array(),
			// meta bag
			is_array($meta) ? $meta : array()
		) as $key=>$value) {
			// assign
			$this->{$key} = $value;
		}
	}
}


/** 
  * (class) Collection
  * Easy access to arrays 
  */

class Collection extends Primitive {

	/** 
	 * (privates)
	 */

	private $buffer = array();
	private $shadow = array(); // used for additional meta data

	/** 
	 * (__construct) 
	 *
     * @param source 		Specify the source of the configration 
     * @param format 		Format of the source, otherwise autodetect
     * @param autoregister 	Auto register variables
	*/

	public function __construct($source = array()) {
		// assign
		$this->apply($source);
	}

	/**
	  * (clear) clears the collection
	  *
	  */

	public function clear() {
		// clear
		$this->buffer = array();
		$this->shadow = array();
	}

	/**
	  * (add) adds to the collection
	  *
	  */

	public function add($name, $data = null, $meta = null) {
		// create key
		$key = $data !== null && !is_integer($name) ? $name : count($this->buffer);
		// create value
		$value = $data === null ? $name : $data;
		// initialize	
		$this->buffer[$key] = $value; 
		// set shadow
		$this->shadow[$key] = new CollectionItem($key, $value, $meta);
	}

	/**
	  * (remove) removes from the collection
	  *
	  */

	public function remove($name) {
		
	}

	/**
	 * (apply) applies an array to the collection
	 *	
	 * @param source 		The source array, can be any format
	 */

	public function apply($source) {
		// clear
		$this->clear();
		// do buffer
		foreach($source as $key=>$value) {
			$this->add($key, $value);
		}
	}


	/** 
	  * (cycle) iterates throught the array
	  * 
	  * @param callback  		a callback handler
	  */

	public function cycle($callback, $recursive = false, $buffer = false) {
		// pass to query routine
		return $this->query(CollectionQuery::all, $callback, $recursive, $buffer);
	}
	


	/**
	  * (query) a simple query routine
	  *
	  */

	public function query($query, $callback, $recursive = false, $buffer = false) {
		// initialize 
		$result = false;
		
		// prepare
		foreach(is_array($buffer) ? $buffer : $this->buffer as $key=>$value) {

			// detect sub array
			if($recursive && (is_array($value) || is_object($value))) {
				// process
				$this->query($query, $callback, true, (array) $value);
			} else {
				// execute callback only on query
				$match = false;

				// process query
				switch(true) {
					case is_array($query):
						// init match
						$match = true;
						// query
						foreach($query as $qkey=>$qval) {
							if(!isset($this->shadow[$key]) || !isset($this->shadow[$key]->{$qkey}) || !Compare($this->shadow[$key]->{$qkey}, $qval)) {
								$match = false;
								break;
							}
						}
						break;

					case CollectionQuery::all: default:
						$match = true;
				}

				// get result
				if($match) {
					// run callback
					$result = $callback($key, $value, isset($this->shadow[$key]) ? $this->shadow[$key] : false);
					// evaluate 
					if($result !== null) break;
				}
			}
		}

		// return result
		return $result;

	}


	/** 
	 * (__get) returns the configuration key
	 * 
     * @param name 		Name of the configuration key
	*/

	public function __get($name) {
		
		return self::__map(array(
			"count" => function() { return count($this->buffer);}
		));
	}

	/** 
	  * (__call) returns the configuration key as type
	  * 
	  * @param name 	Name of the configuration key
	  */

	public function __call($type, $params) {
		
	}
}

