<?php

	namespace Booty\Framework;

	define("DB_NAME", "booty-test");
	define("DB_USER", "root");
	define("DB_PASSWORD", "test");
	define("DB_TABLE", "test");


	# Test for Database 
	class TestDb extends Test {

		function Initialize() {

			$this->db = DB::connect("mysql:host=localhost;dbname=".DB_NAME, DB_USER, DB_PASSWORD); 
		}

		function Assert($condition, $query) {
			$this->AssertTrue($condition, $query->getQuery(false));
		}


		function DatabaseConnectionTest() {

			$this->AssertTrue($this->db->status == DatabaseStatus::connected, "Failed to open connection");

		}

		function DatabaseStatementTest() {

			$this->AssertTrue(DB::table(DB_TABLE)->count() !== false, "Failed to execute statement");

		}

		function DatabaseCreateTest() {

			// create item
			$items = DB::table(DB_TABLE)->create();

			$items->write("name", "Test Item", true);

			
		/*

			$this->id = $items->fetch(DB::id);


			$this->Assert($items->fetch(DB::id) != null, $items);*/

		}

/*

		function DatabaseSelectTest() {

			$items = DB::select(DB_TABLE);

			$this->Assert($items->count() != 0, $items);

		}


		function DatabasePrimaryKeySelectTest() {

			$item = DB::select(DB_TABLE, $this->id); 

			$this->Assert($item->count() != 0, $item);

		}

		function DatabaseSelectWhereTest() {

			$item = DB::select(DB_TABLE)->where(array(DB::id => $this->id));

			$this->Assert($item->count() != 0, $item);
		}



		function DatabaseUpdateTest() {

			if($this->HadSuccess("DatabaseCreateTest")) {

				$items = DB::update(DB_TABLE, $this->id, array("name"=>"Test2"))->result();

				$this->AssertTrue($items->fetch("name" != null, "Could not update a database row"));



			}

			

		}
//*/

	}

