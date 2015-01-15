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
  * (load query library)
  */

require_once(dirname(__FILE__)."/../library/pdo/PDO.php");

/**
  * (constants)
  */

interface DatabaseStatus {
	const drivermissing= -3;
	const failed = -2;
	const invalid = -1;
	const disconnected = 0;
	const connected = 1;
}

interface DatabaseDriverError {
	const credentials = x001;

}


/** 
  * (class) DB/Database
  * This is the primary database object which does everything
  */

class DB extends Primitive {

	/** 
	 * (privates)
	 */

	private $status = DatabaseStatus::disconnected;

	/**
	 * (constants)
	 */

	const success = true;
	const failed = false;

	/** 
	 * (__construct) 
	 *
     * Constructs the database object and opens a connection
     *
	*/
	public function __construct($connection = false, $callback = false) {

		// verify
		if($this->status == DatabaseStatus::connected) return $this->status;

		// initialize
		$result = false;
		$this->status = DatabaseStatus::invalid;

		if(!is_array($connection)) {

			// prepare
			$default = DefaultValue(Application::instance()->configuration->database->default, 0);
			$list = Application::instance()->configuration->database->connections;

			// test
			if(!is_array($list)||!isset($list[$default])) return $this->status;

			// set connection
			$connection = $list[$default];
		}

		// final prepare
		$connection = (object) $connection;

		
		try {
			// initiate PDO
			$this->pdo = new \PDO($connection->dsn, $connection->username, $connection->password);

			// create query builder pdo
			$this->builder = new \BootyPDO($this->pdo);

			// set status
			$this->status = DatabaseStatus::connected;

		} catch(Exception $e) {
			$this->status = DatabaseStatus::failed;
		}
	}


	/**
	 * (PDO Mappings) static mappings to the builder
	 */

	static public function table($table) {
		// connect to instance
		return DB::instance()->builder->from($table);
	}

	static public function create($table, $values = array()) {
		return DB::instance()->builder->create($table, $values);
		
	}

	static public function select($table) {
		return DB::table($table);
	}
}

