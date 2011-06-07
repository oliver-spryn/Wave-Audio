<?php
/*
 * This class includes a number of random, uncategorized methods to ease development elseware in the system:
 *  - redirect: Redirect the user to another page using HTTP headers
*/

	class Misc {
	//Redirect the user to another page using HTTP headers
		public static function redirect($URL) {
			header("Location: " . URL);
			exit;
		}
	}
?>