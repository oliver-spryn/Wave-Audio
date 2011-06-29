<?php
/*
 * This is one of the most essential and heavily used classes within the system. This class creates 
 * a connection to a database, and provides all of the necessary methods to safely and easily create, 
 * read, update, and delete data from the database:
 *  - __construct: The constructor method which creates the connection to the database
 *  - __desctruct: The destructor method which will close out a connection to the MySQL server at the end of each page
 *  - query: Run a basic query on the database
 *  - escape: Escape a value for safe storage in the database
 *  - prepare (Private): Prepare stored database values for display
 *  - fetch: Fetch the result of a database query and clean-up all of the values for display
 *  - quick: Run a basic query on the database, and fetch the result
 *  - RUDBase (Private): This is a base method to Read, Update, and Delete (RUD) database entries
 *  - insert: A specialized method for inserting entries into a database, not for modifying a database or table structure
 *  - read: A specialized method for reading database values
 *  - update: A specialized method for updating database values, not for modifying a database or table structure
 *  - delete: A specialized method for deleting database values, not for modifying a database or table structure
 *  - exist: A specialized method for checking if a database value exists
*/
	
class Database {
//These variables are only used by the system for internal linking
	private $connection;
	
//The constructor method which creates the connection to the database
	public function __construct() {
		global $config;
		
	//Instantiate the "mysqli" class and connect to the database
		$this->connection = new mysqli($config->dbHost, $config->dbUsername, $config->dbPassword, $config->dbName, $config->dbPort);
		
	/*
	 * Check to see if the connection to the database was successful. Checking for
	 * "$this->connection->connect_error" is the offical object-oriented way to do it. However, this
	 * instance variable was broken until PHP 5.2.9 and 5.3.0. To ensure maxmium compatibility, 
	 * with older versions of PHP, the procedural way of checking the "mysqli_connect_error()"
	 * function can be used. However, since these issues were fixed back in 2009, no "good" host should
	 * be hosting such an old version of PHP.
	*/
		try {
			if ($this->connection->connect_error) {
				throw new Exception("<strong>Fatal error:</strong> The system could not connect to the database server. Please ensure that your database login credentials are correct, and that the server is not offline.
<br /><br />
[Error code] " . $this->connection->connect_errno . "
<br />
[Error message] " . $this->connection->connect_error);
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
//The destructor method which will close out a connection to the MySQL server at the end of each page
	public function __destruct() {
		$this->connection->close();
	}
	
//Run a basic query on the database
	public function query($query) {
	//Run a query on the database an make sure is executed successfully
		try {
			if ($result = $this->connection->query($query, MYSQLI_USE_RESULT)) {
				return $result;
			} else {
				$error = debug_backtrace();
				
				throw new Exception("<strong>Warning:</strong> There is an error with your query:
<br /><br />
<strong>[Query]</strong> " . $query . "
<br />
<strong>[MySQL Error]</strong> " . $this->connection->error . "
<br />
<strong>[Error on line]</strong> " . $error['0']['line'] . "
<br />
<strong>[Error in file]</strong> " . $error['0']['file']);
			}
		} catch (Exception $e) {
			$this->connection->close();
			
			die($e->getMessage());
		}
	}
	
//Escape a value for safe storage in the database
	public function escape($input) {
		return $this->connection->real_escape_string($input);
	}
	
//Prepare stored database values for display
	private function prepare($input, $htmlEncode = false, $stripSlashes = true) {		
		$stripSlashes == true ? $input = stripslashes($input) : false;
		$htmlEncode == true ? $input = htmlentities($input) : false;
		
		return $input;
	}
	
//Fetch the result of a database query and clean-up all of the values for display
	public function fetch($result, $fetchType = MYSQLI_ASSOC) {
	//Fetch the array
		$result = $result->fetch_array($fetchType);
		
		if ($result && is_array($result)) {
		/*
		 * The loop below has several purposes. It will:
		 *  - replace all non-secure links, such as images and URLs, with secure links, if the current page is encrypted
		 *  - clean-up escaped values from the database
		*/
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
	
//Return the number of rows from a query
	public function num($result) {
		return $result->num_rows;
	}
	
//Run a basic query on the database, and fetch the result
	public function quick($query, $fetchType = MYSQLI_BOTH) {
		$result = $this->query($query);
		return $this->fetch($result, $fetchType);
	}
	
/*
 * The methods beyond this point are highly specialized to perform specific types of queries on a database.
 * The above "query()" method's purpose is more general, and is best suited for queries like: "DROP TABLE 
 * `mytable`". The methods below are accustomed to handling more complex input, such as unknown mixture of 
 * strings and arrays, and parsing them into a query which is completly safe to create, read, update, 
 * or delete entries, with minimial effort for future use. These methods do use the "query()" method when 
 * they are ready to execute their query.
*/
	
//This is a base method to Read, Update, and Delete (RUD) database entries
	private function RUDBase($input) {
		$query = "";
		
	//Parse the input in this loop
		foreach($input as $argument) {
		//Strings are simple to parse!
			if (is_string($argument)) {
			//Trim any whitespace before appending this string to the query
				$query .= trim($argument) . " ";
			}
			
		//Arrays require more logic
			if (is_array($argument)) {
				$values;
				
				foreach($argument as $key => $value) {
					$values .= "`" . $key . "` = '" . $this->escape($value) . "', ";
				}
				
				$query .= rtrim($values, ", ") . " ";
			}
		}
		
	//Finally run the parsed query
		return $this->query(rtrim($query));
	}
	
//A specialized method for inserting entries into a database, not for modifying a database or table structure
	public function insert() {
		$query = "";
		$firstArrayParsed = false;
		
	//Since there is an unknown number of values, then grab all of the supplied arguments...
		$arguments = func_get_args();
		
	// ... and parse them in this loop
		foreach($arguments as $argument) {
		//Strings are simple to parse!
			if (is_string($argument)) {
			//Trim any whitespace before appending this string to the query
				$query .= trim($argument) . " ";
			}
			
		//Arrays require more logic
			if (is_array($argument)) {				
			/*
			 * If there are multiple arrays in the supplied arguments, then the *first* array will contain the values to be inserted,
			 * according to SQL standard conventions. Before parsing the array into the query, check to see if the "$firstArrayParsed"
			 * variable is "true" and process it accordingly.
			*/
				try {
					$keys = "";
					$values = "";
					
				//Has the INSERT portion been parsed already?
					if (!$firstArrayParsed) {
						$firstArrayParsed = true;
					} else {
						throw new Exception("The INSERT portion has been parsed");
					}
						
					foreach($argument as $key => $value) {
						$keys .= "`" . $key . "`, ";
						
					//json_encode() is a tad faster than serialize()
						$values .= is_array($value) ? "'" . $this->escape(json_encode($value)) . "', " : "'" . $this->escape($value) . "', ";
					}
					
					$query .= "( " . rtrim($keys, ", ") . " ) VALUES ( " . trim($values, ", ") . ") ";
				} catch (Exception $e) {
					$values = "";
					
					foreach($argument as $key => $value) {
						$values .= "`" . $key . "` = '" . $this->escape($value) . "', ";
					}
					
					$query .= rtrim($values, ", ") . " ";
				}
			}
		}
		
	//Finally run the parsed query
		return $this->query(rtrim($query)) ? true : false;
	}
	
//A specialized method for reading database values
	public function read() {
	//Since there is an unknown number of values, then grab all of the supplied arguments...
		$arguments = func_get_args();
		
	// ... and execute them in the base method
		$result = $this->RUDBase($arguments);
		
	//Finally return the result
		return $this->escape($result);
	}
	
//A specialized method for updating database values, not for modifying a database or table structure
	public function update() {
	//Since there is an unknown number of values, then grab all of the supplied arguments...
		$arguments = func_get_args();
		
	// ... and execute them in the base method
		return $this->RUDBase($arguments) ? true : false;
	}

//A specialized method for deleting database values, not for modifying a database or table structure
	public function delete() {
	//Since there is an unknown number of values, then grab all of the supplied arguments...
		$arguments = func_get_args();
		
	// ... and execute them in the base method
		return $this->RUDBase($arguments) ? true : false;
	}
	
//A specialized method for checking if a database value exists
	public function exist($query) {
		$query = $this->connection->query($query);
	
		return $query->num_rows > 0 ? true : false;
	}
}
	
//Instantiate the "Database" class to allow the system easily communicate with the database.
	$database = new Database();
	$db = $database;
?>