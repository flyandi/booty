<?php

	namespace Booty\Framework;


	# Test for Database 
	class TestDb extends Test {

		function Initialize() {

			$this->db = DB::connect("mysql:host=localhost;dbname=fbo", "root", "terranova"); 
		}


		function DatabaseConnectionTest() {

			
			$this->AssertTrue($this->db->status == DatabaseStatus::connected, "Failed to open connection");

		}

		function DatabaseSelectTest() {

			$items = DB::select("lead");

			$this->AssertTrue($items->count() != 0, "No rows where selected");

		}

		function DatabaseSpecificSelectTest() {

			$items = DB::select("lead")->where(array("name"=>"Test"));

			$this->AssertTrue($items->count() != 0, "Specific row was not selected");

		}

		function DatabaseCreateTest() {


			$items = DB::create("lead");

			$this->AssertTrue($items->Read(DB::id) != null, "Could not create a database row");

		}


	}

