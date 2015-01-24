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
  * (interfaces) 
  */

interface QueryResult {
	const raw = 0;
	const single = 2;
	const stream = 1;
}


/** 
  * (class) Resources
  * This is the primary object
  */

class Query {

	/**
	 * Keyword formulars
	 */

	const select = 'SELECT';
	const update = 'UPDATE';
	const delete = 'DELETE';
	const insert = 'INSERT INTO';
	const set = 'SET';
	const values = 'VALUES';
	const from = 'FROM';
	const where = 'WHERE';
	const reset = null;

	/**
	  * Fields
	  */

	const idstring = 'idstring';
	const id = Query::idstring;

	/** 
	 * (privates) 
	 */

	private $table = false;
	private $id = false;
	private $updated = false;
	private $statements = array();
	private $rows = array();
	private $row = array();
	private $query = false;
	private $object = false;
	private $pdo = false;
	private $raw = false;
	private $currentrow = false;
	private $currentclause = false;

	/**
	  * (publics)
	  */

	public $status = false;
	public $type = false;


	/** 
	 * (__construct) 
	 *
     * @param source 		Specify the source of the configration 
	 */
	public function __construct($table, $id = null) {
		// assign
		$this->table = $table;
		$this->id = $id;

		// clear
		$this->Clear();

		// set table
		$this->From($table);

		// check id
		if($this->id) {
			$this->FromId($id);
		}

	}

	/** 
	 * Properties
	 */

	public function Clear() {

		$this->statements = array();
	}


	public function Count() {

		$that = clone $this;
		
		$result = $that->Select(Query::reset)->Select("COUNT(*) as count")->__execute(Query::select, QueryResult::single);

		if($result->status) {
			return $result->count;
		}

		return false;
	}

	/** 
	 * I/O
	 */

	public function Publish($row = false) {

		if($this->currentclause != Query::select) return; 

		// initialize
		$values = false;

		// get values
		switch($this->type) {
			
			case QueryResult::single:
				$row = 0;

			default:	
				$row = $row === false ? $this->currentrow : $row;
				$values = isset($this->rows[$row]) ? $this->rows[$row] : false;
				break;
		}

		if(is_array($values)) {
			$this->Update($values);
		}

	}

	public function Write($row, $name, $value = false, $publish = false) {

		// write to row model
		switch($this->type) {

			case QueryResult::single:
				if(!is_array($row)) {
					$values = array($row => $name);
					$publish = $value;
				} else {
					$values = $row;
					$publish = $name;
				}
				$row = 0;
				break;

			default:
				if(isset($this->rows[$row])) {
					if(!is_array($name)) {
						$values = array($name => $value);
					} else {
						$values = $name;
						$publish = $value;
					}
				} else {
					return false;
				}
				break;
		}
		// set data
		foreach($values as $name => $value) {
			$this->rows[$row][$name] = $value;
		}

		// update
		if($publish) $this->Publish();
	}

	public function Read($row, $name = false, $default = false) {

		// write to row model
		switch($this->type) {

			case QueryResult::single:
				$default = $name;
				$name = $row;
				$row = 0;

			default:
				return isset($this->rows[$row]) && isset($this->rows[$row][$name]) ? $this->rows[$row][$name] : $default;
				break;
		}

	}

	public function everything($auto = true) {
		switch($this->type) {
			case QueryResult::single:
				return isset($this->rows[0]) && is_array($this->rows[0]) ? ($auto ? (object) $this->rows[0] : $this->rows[0]) : false;
				break;

			default:
				return is_array($this->rows) ? $this->rows : false;
				break;
		}
	}


	/** 
	 * Keywords
	 */

	public function FromId($id) {
		$this->reset(Query::where)->Where(Query::id, $id)->select()->__execute(Query::select, QueryResult::single);
	}

	public function From($table) {
		$this->__clause(Query::from, $table);
	}

	public function Select($conditions = false) {
		return $this->__clause(
			Query::select, 
			$conditions === null ? null : ($conditions === false) ? "*" : $conditions,
			null
		);
	}

	public function Create($values = array()) {
		return $this->Insert(array_merge(is_array($values) ? $values : array(), array(Query::id => CreateGUID())));
	}

	public function Insert($values = array()) {

		$this->reset(Query::values)->__clause(Query::values, $values, null)->__execute(Query::insert);

		$this->reset(Query::select)->Select()->Where($values)->__execute(Query::select, QueryResult::single);

		return $this;

	}

	public function Where($conditions, $parameters = array()) {
		return $this->__clause(Query::where, $conditions, $parameters);
	}

	public function Update($values = null) {
		return $this->reset(Query::set)->__clause(Query::set, $values, null)->__execute(Query::update);
	}

	public function Delete() {
		return $this->__execute(Query::delete);
	}


	/**
	 * (__clause)
	 */

	private function __clause($clause, $condition, $parameters = array()) {

		$clause = strtoupper($clause);

		switch(true) {

			// Clear clause
			case ($condition === null) || $condition == Query::reset: 
				$this->__reset($clause);
				break;

			// array
			case is_array($condition):
				// cycle
				foreach($condition as $key => $value) {
					$this->__clause($clause, $key, $value);
				}
				break;

			// key - value pair 
			case is_string($parameters):
				$this->__statement($clause, is_numeric($condition) ? array($condition => $parameters) : $condition, !is_numeric($condition) ? $parameters : null);
				break;

				/*
			case is_array($parameters) && count($parameters) > 0: 
				// check condition
				if(preg_match('~^(NOT )?`?[a-z_:][a-z0-9_.:]*`?$~i', $condition)) {

					if (is_null($parameters)) {
						$this->__statement($clause, "$condition is NULL");
						break;

					} elseif (is_array($condition)) {
						$in = $this->__quote($condition);
						$this->__statement($clause, "$condition IN $in");
						break;
					} 

					$condition = "$condition = ?";
				}*/

			default:
				$this->__statement($clause, $condition);
				break;

		}

		return $this;
	}

	/**
	 * (__statements)
	 */

	public function reset() {

		foreach(func_get_args() as $clause) {
			$this->__reset($clause);
		}

		return $this;
	}

	private function __reset($clause) {

		$this->statements[$clause] = array();

	}

	private function __statement($clause, $statement, $value = null) {

		$clause = strtoupper(trim(str_replace(" ", "", $clause)));

		if(!isset($this->statements[$clause])) $this->statements[$clause] = array();

		if($value !== null) {
			$this->statements[$clause][$statement] = $value;
		} else {
			$this->statements[$clause][] = $statement;
		}
	}


	/** 
	 * Magics
	 */

	public function __get($name) {


	}

	public function __set($name, $value) {

	}

	public function __call($name, $arguments) {
		
		$this->__clause($name, $arguments);
	}

	/**
	 * (__execute)
	 */

	private function __execute($clause, $request = null) {

		// query
		if(!in_array($clause, array(Query::update))) {

			// reset clause
			$this->currentclause= $clause;

			// reset rows
			$this->rows = array();

			// reset status
			$this->status = false;
		}

		// acquire reference to pdo
		$this->pdo = DB::pdo();
		
		// retrieve query
		if($clause !== false) $query = $this->__query($clause);

		// prepare query
		$result = $this->pdo->prepare($query);

		if ($this->object !== false) {
			if (class_exists($this->object)) {
				$result->setFetchMode(\PDO::FETCH_CLASS, $this->object);
			} else {
				$result->setFetchMode(\PDO::FETCH_OBJ);
			}
		} elseif ($this->pdo->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE) == \PDO::FETCH_BOTH) {
			$result->setFetchMode(\PDO::FETCH_ASSOC);
		}

		$time = microtime(true);
		if ($result && $result->execute()) {
			$this->time = microtime(true) - $time;
		} else {
			$result = false;
		}

		$this->raw = $result;

		if($result) {
			// set type
			if($request !== null) $this->type = $request;

			// parse result
			switch($request) {

				case QueryResult::single:

					// get result
					$row = $result->fetchall();

					// parse result
					$this->rows = array(isset($row[0]) && is_array($row[0]) ? $row[0] : false);

					$this->status = is_array($this->rows[0]);

					// return result
					return (object)array_merge(is_array($this->rows[0]) ? $this->rows[0] : array(), array(
						"status" => $this->status
					));

					break;
			}
		}

		return $result;
	}


	/**
	 * (__query)
	 */

	private function __query($clause) {

		$that = $this;
		$pattern = false;
		$query = array();
		$__clause = $clause;

		switch($clause) {

			// (Delete)
			case Query::delete:

				$pattern = array(
					"DELETE" => function($statements) use ($that) {
						return Query::delete;
					},
					"FROM" => null,
					"WHERE" => " AND ",
				);

				break;

			// (Update)
			case Query::update:

				$pattern = array(
					"UPDATE" => function($statements) use ($that) {
						return sprintf("%s %s", Query::update, $that->table);
					},
					"SET" => ",",
					"WHERE" => " AND "
				);

				break;
			
			// (Select)
			case Query::select:

				$pattern = array(
					"SELECT" => ', ',
					"FROM" => null,
					//'JOIN' => array($this, 'getClauseJoin'),
					"WHERE" => ' AND ',
					"GROUP BY" => ',',
					"HAVING" => ' AND ',
					"ORDER BY" => ', ',
					"LIMIT" => null,
					"OFFSET" => null
				);

				break;

			// (Insert Into)
			case Query::insert:

				$pattern = array(
					"INSERT INTO" => function($statements) use ($that) {

						$values = DefaultValue(@$that->statements[Query::values], false);

						return is_array($values) ? sprintf("%s %s (%s)", 
							Query::insert,
							$that->table,
							implode(",", array_keys($values))
						) : null;
					},
					"VALUES" => function($statements) use ($that) {

						return sprintf("%s %s", 
							Query::values,
							$that->__quote($statements)
						);
					}
				);

				break;

			default:

				return false;
		}

		// build query
		foreach($pattern as $_clause => $separator) {

			$clause = strtoupper(trim(str_replace(" ", "", $_clause)));

			if(isset($this->statements[$clause]) || in_array($_clause, array(Query::update, Query::insert, Query::delete))) {

				// get statement
				$statements = DefaultValue(@$this->statements[$clause], false);


				// switch true
				switch(true) {

					case is_callable($separator):
						if($result = $separator($statements)) $query[] = $result;
						break;

					case is_array($statements) && count($statements) > 0:

						$list = array();

						foreach($statements as $index=>$value) {
							if(!in_array($index, $this->__filterlist($clause))) {
								$list[] = is_string($index) ? sprintf("%s=%s", $index, $this->__quote($value)) : $value;
							}
						}

						$query[] = sprintf("%s %s", $clause, implode($separator, $list));
						break;

					case $separator == null: 

						$query[] = sprintf("%s %s", $clause, is_array($statements) && count($statements) > 0 ? $statements[0] : $statements);
						break;
				}
			}

		}

		// finalize query
		return $this->query = implode(" ", $query);

	}

	/**
	  * (__quote)
	  */

	private function __quote($value) {

		switch(true) {
			case !isset($value): 
				return "NULL";
				break;

			case is_array($value):	
				return "(" . implode(", ", array_map(array($this, '__quote'), $value)) . ")";
				break;
		}

		// format value
		$value = $this->__format($value);

		// switch by type
		switch(true) {

			case is_float($value):
				return sprintf("%F", $value); 
				break;

			case $value === false:
				return 0;
				break;

			case $value === true:
				return 1;
				break;

			case $this->__iskeyword($value):
				return (string) $value;
				break;

			default:
				return $this->pdo->quote($value);
		}
	}

	
	/**
	  * (__format)
	  */

	private function __format($value) {
		// switch by type
		switch(true) {
			case $value instanceof DateTime:
				return $value->format("Y-m-d H:i:s"); 
				break;
		}

		return $value;
	}

	/**
	  * (__iskeyword)
	  */

	private function __iskeyword($value) {

		return false;
	}


	/**
	  * (__filterlist)
	  */

	private function __filterlist($clause) {
		switch($clause) {

			case Query::set:
				return array(Query::idstring);
				break;
		}

		return array(); // empty
	}

	

}