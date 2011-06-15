<?php
/*
 * This class includes a number of random, uncategorized methods to ease development elseware in the system:
 *  - redirect: Redirect the user to another page using HTTP headers
 *  - randomValue: Generate a random value
*/

class Misc {
//Redirect the user to another page using HTTP headers
	public static function redirect($URL) {
		header("Location: " . $URL);
		exit;
	}
	
//Generate a random value
	public static function randomValue($length = 10, $seeds = "alphaNumeric") {
		$seedings['alpha'] = "abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$seedings['numeric'] = "0123456789";
		$seedings['alphaNumeric'] = "abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$seedings['anyCharacter'] = "abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`~!@#%^&*()-_=+[{]}|;:',<.>/?";
		$seedings['hexidecimal'] = "0123456789abcdef";
		
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		} else {
			die("An invalid random value type was requested.");
		}
		
		list($usec, $sec) = explode(" ", microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		$string = "";
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++) {
			$string .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $string;
	}
}
?>