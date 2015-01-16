<?php

	namespace Booty\Framework;

	define("DB_NAME", "booty-test");
	define("DB_USER", "root");
	define("DB_PASSWORD", "terranova");
	define("DB_TABLE", "test");


	# Test for Database 
	class TestDb extends Test {

		function Initialize() {

			$this->db = DB::connect("mysql:host=localhost;dbname=".DB_NAME, DB_USER, DB_PASSWORD); 
		}


		function DatabaseConnectionTest() {

			
			$this->AssertTrue($this->db->status == DatabaseStatus::connected, "Failed to open connection");

		}


		function DatabaseCreateTest() {


			$items = DB::create(DB_TABLE);

			$this->id = $items->fetch(DB::id);


			$this->AssertTrue($items->fetch(DB::id) != null, "Could not create a database row");

		}


		function DatabaseSelectTest() {

			$items = DB::select(DB_TABLE);

			$this->AssertTrue($items->count() != 0, "No rows where selected");

		}



		function DatabaseSpecificSelectTest() {


			$items = DB::select(DB_TABLE)->where(array("name"=>"Test"));

			$this->AssertTrue($items->count() != 0, "Specific row was not selected");

		}




/*
		function DatabaseUpdateTest() {

			if($this->HadSuccess("DatabaseCreateTest")) {

				$items = DB::update(DB_TABLE, $this->id, array("name"=>"Test2"))->execute();

				$this->AssertTrue($items->fetch("name" != null, "Could not update a database row"));



			}

			

		}
//*/

	}

