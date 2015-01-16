<?php

	namespace Booty\Framework;


	# Test for Database 
	class TestDb extends Test {

		function Initialize() {

			$this->db = DB::connect("mysql:host=localhost;dbname=leads-local", "root", "test"); 
		}


		function DatabaseConnectionTest() {

			
			$this->AssertTrue($this->db->status == DatabaseStatus::connected, "Failed to open connection");

		}

		function DatabaseSelectTest() {

			$items = DB::select("campaigns");

			$this->AssertTrue($items->count() != 0, "No rows where selected");

		}

		function DatabaseSpecificSelectTest() {

			$items = DB::select("campaigns")->where(array("name"=>"Test"));

			$this->AssertTrue($items->count() != 0, "Specific row was not selected");

		}


		function DatabaseCreateTest() {


			$items = DB::create("campaigns");

			$this->id = $items->fetch(DB::id);


			$this->AssertTrue($items->fetch(DB::id) != null, "Could not create a database row");

		}


		function DatabaseUpdateTest() {

			if($this->HadSuccess("DatabaseCreateTest")) {

				$items = DB::update("campaigns", $this->id, array("name"=>"Test2"))->execute();

				$this->AssertTrue($items->fetch("name" != null, "Could not update a database row"));



			}

			

		}


	}

