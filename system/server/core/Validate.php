<?php
/*
 * This class will validate user input prior to entry to a database:
 *  - required: The most simple form of validation, which ensures a value has been provided
 *  - isArray: Validate all the values of an array, and optionally provide a targeted size
*/

	class Validate {
		private $redirect = " Click <a href=\"javascript:window.location = document.location.href\">here</a> to retry.";
		
	//The most simple form of validation, which ensures a value has been provided
		public static function required($string) {
			if(!empty($string)) {
				return true;
			} else {
				die("A required value was empty." . $this->redirect);
			}
		}
		
	//Validate all the values of an array, and optionally provide a targeted size
		public static function isArray($array, $size = false) {
			if (!empty($array) && is_array($array) && count($array) == $size) {
				for($count = 0; $count <= count($array); $count ++) {
					self::required($array[$count]);
				}
			} else {
				die("A required value was empty." . $this->redirect);
			}
		}
	}
?>