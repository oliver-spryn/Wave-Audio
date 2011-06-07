<?php
//Create a connection to the local database, and provide all of the necessary methods to safely create, read, update, and delete data from the database.
	
	class Database {
	//Connect to the database
		function __construct() {
			global $config;

			$this->connection = mysql_connect($config->dbHost . ":" . $config->dbPort, $config->dbUserName, $config->dbPassword);
			
			if (!$this->connection) {
				die("<strong>Fatal error:</strong> The system could not connect to the database server. Please ensure that your database login credentials are correct, and that the server is not offline.\n<br /><br />\n" . mysql_error());
			}
			
			$dbSelect = mysql_select_db($config->dbName, $this->connection);
			
			if (!$dbSelect) {
				die("<strong>Fatal error:</strong> The system could not select the database. Please ensure that your database name is correct.\n<br /><br />\n" . mysql_error());
			}
		}
		
	//Clean-up stored database values
		public function prepare($input, $htmlEncode = false, $stripSlashes = true) {
			global $message;
			
			if ($stripSlashes == true) {
				if ($htmlEncode == true) {
					return htmlentities(stripslashes($input));
				} else {
					return stripslashes($input);
				}
			} else {
				if ($htmlEncode == true) {
					return htmlentities($input);
				} else {
					$error = debug_backtrace();
					die("<strong>Warning:</strong> No action was performed on the returned database value on line " .  $error['0']['line']);
				}
			}
		}
		
	//Run a "mysql_fetch_array()" command when the query selects all columns, or "mysql_fetch_assoc()" when only a few columns are selected
		public function fetch($value, $fetchType = "array") {
			if ($fetchType == "array") {
				$result = mysql_fetch_array($value);
			} else {
				$result = mysql_fetch_assoc($value);
			}
			
			if ($result) {
				$return = array();
				
				foreach ($result as $key => $value) {
					if (PROTOCOL == "https://") {	
						$return[$key] = str_replace(str_replace("https://", "http://", ROOT), ROOT, $this->prepare($value));
					} else {
						$return[$key] = $this->prepare($value);
					}
				}
				
				return $return;
			} else {
				return false;
			}
		}
		
	//Run and return a cleaned up version of "mysql_query()"
		public function query($query, $returnType = false, $showError = true) {
			$action = mysql_query($query, $this->connection);
			
		//If no value was returned from the query
			if (!$action) {
				if ($showError == true) {
					$error = debug_backtrace();
					die("<strong>Warning:</strong> There is an error with your query: \n" . $query . "\n<br /><br />\n" . mysql_error() . "\n<br /><br />\nError on line: " . $error['0']['line'] . "<br />\nError in file: " . $error['0']['file']);
				} else {
					return false;
				}
			} else {
				if (!strstr($query, "INSERT INTO") && !strstr($query, "UPDATE") && !strstr($query, "SET") && !strstr($query, "CREATE TABLE") && !strstr($query, "ALTER TABLE") && !strstr($query, "DROP TABLE")) {
					switch($returnType) {
					//Fetch an array, and clean-up each value for display, DEFAULT BEHAVIOR
						case false : 
						case "array" : 
							if ($result = $this->fetch($action)) {
								//Do nothing, the array was sucessfully extracted
							} else {
								if ($showError == true) {
									$error = debug_backtrace();
									die("<strong>Warning:</strong> There is an error with your query: \n" . $query . "\n<br /><br />\nError on line: " . $error['0']['line'] . "<br />\nError in file: " . $error['0']['file']);
								}
							}
							
							if (is_array($result) && !empty($result)) {
								return $result;
							} else {
								return false;
							}
							
							break;
							
					//Return the raw resource
						case "raw" : 
							return $action;
							break;
								
					//Return the number of rows
						case "num" : 
							$result = mysql_num_rows($action);
							return $result;
							break;
							
					//Fetch the array, where only certain rows are selected
						case "selected" : 
							if ($result = fetch($action, "assoc")) {
								//Do nothing, the array was sucessfully extracted
							} else {
								if ($showError == true) {
									$error = debug_backtrace();
									die("<strong>Warning:</strong> There is an error with your query: \n" . $query . "\n<br /><br />\nError on line: " . $error['0']['line'] . "<br />\nError in file: " . $error['0']['file']);
								}
							}
							
							if (is_array($result) && !empty($result)) {
								return $result;
							} else {
								return false;
							}
							
							break;
							
					//Return an error if an unsupported return-type is requested
						default : 
							$error = debug_backtrace();
							die("<strong>Warning:</strong> An invalid query return-type was requested on line " .  $error['0']['line']);
							break;
					}
				}
			}
		}
	}
	
//Instantiate the "Database" class to allow the system easily communicate with the database.
	$database = new Database();
	$db = $database;
?>