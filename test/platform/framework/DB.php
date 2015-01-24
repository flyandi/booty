<?php

	namespace Booty\Framework;

	define("DB_NAME", "booty-test");
	define("DB_USER", "root");
	define("DB_PASSWORD", "test");
	define("DB_TABLE", "test");


	# Test for Database 
	class TestDb extends Test {

		public $id = false;

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

			$this->set("id", $items->Read(Query::id));

		}



		function DatabaseSelectTest() {

			// create item
			$items = DB::table(DB_TABLE, $this->get("id"));

			$this->AssertTrue($items->Read("name") == "Test Item", "Failed to select the newly created test item.");

		}


		function DatabaseUpdateTest() {

			// create item
			$item = DB::table(DB_TABLE, $this->get("id"));

			$item->write("value", "New Value", true);

			$this->AssertTrue($item->Read("value") == "New Value", "Failed to update the database item");

		} 


		function DatabaseArrayUpdateTest() {

			// create item
			$item = DB::table(DB_TABLE, $this->get("id"));

			$item->write(array(
				"name" => "New Name",
				"value" => "Super Value"
			), true);

			$this->AssertTrue($item->Read("value") == "Super Value", "Failed to array update the database item");

		}

		function DatabaseDeleteTest() {

			// create item
			DB::table(DB_TABLE, $this->get("id"))->Delete();

			$this->AssertTrue(DB::table(DB_TABLE, $this->get("id"))->status === false, "Failed to delete the database item");

		}


	}

