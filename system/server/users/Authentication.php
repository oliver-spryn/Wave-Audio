<?php
//Manage user authentication, such as logging in, logging out, failed logins, etc...

	class Authentication {
	//Login a user
		public static function login($redirectSuccess, $redirectFailed) {
			global $db, $config;
			
			if (self::loggedIn() && isset($_POST['username']) && isset($_POST['password'])) {
			//Check the database for a matching username and password
				$result = $db->query("SELECT * FROM `users` WHERE", array(
					"username" => Validate::required($_POST['username']),
					"password" => Validate::required(md5($_POST['password'] . $config->encryptedSalt))
				));
				
			//If a match is found, then set the sessions and login
				if ($result) {
					$_SESSION['username'] = $result['username'];
					$_SESSION['role'] = $result['role'];
					
					Misc::redirect($redirectSuccess);
				} else {
					Misc::redirect($redirectFailed);
				}
			}
		}
		
	//Logout a user
		public static function logout() {	
			global $config;
			
			if (self::loggedIn()) {
			//Find all of the sessions and manually unset them
				foreach ($_SESSION as $key => $value) {
					$_SESSION[$key] = NULL;
					unset($_SESSION[$key]);
				}
				
				$_SESSION = array();
				
				session_unset();
				
			//Remove the session cookie
				$cookieData = session_get_cookie_params();
				
				setcookie("CMS_" . $config->sessionSuffix, "", time() - 3600, $cookieData['path'], $cookieData['domain'], $cookieData['secure'], $cookieData['httponly']);
				
			//Destroy the session
				session_destroy();
			}
			
			Misc::redirect(ROOT);
		}
		
	//Check login status
		public static function loggedIn() {
			if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
				return true;
			} else {
				return false;
			}
		}
		
	//Log a failed login attempt
		public static function logAttempt($userName) {
			global $db;
			
			$db->query("INSERT INTO `loginfailed`", array(
				"timeStamp" => time(),
				"userName" => Validate::required($userName),
				"IPAddress" => $_SERVER['REMOTE_ADDR']
			));
		}
		
	//Get the information of the logged-in user
		public static function userInfo() {
			global $db;
			
			if (self::loggedIn()) {
				$info = $db->query("SELECT * FROM `users` WHERE", array(
					"username" => $_SESSION['username'],
					"role" => $_SESSION['role']
				));
				
				return array(
					"id" => $info['id'],
					"username" => $info['username'],
					"role" => $info['role'],
					"IPAddress" => $info['IPAddress'],
					"name" => $info['firstName'] . " " . $info['lastName'],
					"firstName" => $info['firstName'],
					"lastName" => $info['lastName'],
					"emailAddress" => $info['emailAddress1']
				);
			} else {
				return false;
			}
		}
	}
?>