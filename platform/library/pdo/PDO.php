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
  * BootyPDO is based on FluentPDO and is refactored for the use with
  * the Booty Framework.
  */

/**
  * Includes
  */

include_once 'PDOStructure.php';
include_once 'PDOUtils.php';
include_once 'PDOLiteral.php';
include_once 'PDOBaseQuery.php';
include_once 'PDOCommonQuery.php';
include_once 'PDOSelectQuery.php';
include_once 'PDOInsertQuery.php';
include_once 'PDOUpdateQuery.php';
include_once 'PDODeleteQuery.php';

/**
  * Base Class BootyPDO
  */

class BootyPDO {

	private $pdo, $structure;

	/** @var boolean|callback */
	public $debug;

	function __construct(PDO $pdo, FluentStructure $structure = null) {
		$this->pdo = $pdo;
		if (!$structure) {
			$structure = new FluentStructure;
		}
		$this->structure = $structure;
	}

	/** Create SELECT query from $table
	 * @param string $table  db table name
	 * @param integer $primaryKey  return one row by primary key
	 * @return \SelectQuery
	 */
	public function from($table, $primaryKey = null) {
		$query = new SelectQuery($this, $table);
		if ($primaryKey) {
			$tableTable = $query->getFromTable();
			$tableAlias = $query->getFromAlias();
			$primaryKeyName = $this->structure->getPrimaryKey($tableTable);
			$query = $query->where("$tableAlias.$primaryKeyName", $primaryKey);
		}
		return $query;
	}

	/** Create CREATE query
	 *
	 * @param string $table
	 * @param array $values  you can add one or multi rows array @see docs
	 * @return \CreateQuery
	 */
	public function create($table, $values = array()) {
		$query = new InsertQuery($this, $table, array_merge($values, array(
			"idstring" => CreateGUID()
		)));
		return $query;
	}

	/** Create INSERT INTO query
	 *
	 * @param string $table
	 * @param array $values  you can add one or multi rows array @see docs
	 * @return \InsertQuery
	 */
	public function insertInto($table, $values = array()) {
		$query = new InsertQuery($this, $table, $values);
		return $query;
	}

	/** Create UPDATE query
	 *
	 * @param string $table
	 * @param array|string $set
	 * @param string $primaryKey
	 *
	 * @return \UpdateQuery
	 */
	public function update($table, $set = array(), $primaryKey = null) {
		$query = new UpdateQuery($this, $table);
		$query->set($set);
		if ($primaryKey) {
			$primaryKeyName = $this->getStructure()->getPrimaryKey($table);
			$query = $query->where($primaryKeyName, $primaryKey);
		}
		return $query;
	}

	/** Create DELETE query
	 *
	 * @param string $table
	 * @param string $primaryKey  delete only row by primary key
	 * @return \DeleteQuery
	 */
	public function delete($table, $primaryKey = null) {
		$query = new DeleteQuery($this, $table);
		if ($primaryKey) {
			$primaryKeyName = $this->getStructure()->getPrimaryKey($table);
			$query = $query->where($primaryKeyName, $primaryKey);
		}
		return $query;
	}

	/** Create DELETE FROM query
	 *
	 * @param string $table
	 * @param string $primaryKey
	 * @return \DeleteQuery
	 */
	public function deleteFrom($table, $primaryKey = null) {
		$args = func_get_args();
		return call_user_func_array(array($this, 'delete'), $args);
	}

	/** @return \PDO
	 */
	public function getPdo() {
		return $this->pdo;
	}

	/** @return \FluentStructure
	 */
	public function getStructure() {
		return $this->structure;
	}
}
