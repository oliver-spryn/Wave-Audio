<?php
//This class will validate user input prior to entry to a database.

	class Validate {
		public static $redirect = " Click <a href=\"javascript:window.location = document.location.href\">here</a> to retry.";
		
		public static function required($string) {
			if(!empty($string)) {
				return true;
			} else {
				die("A required value was empty." . self::$redirect);
			}
		}
		
		public static function isArray($array, $size = false) {
			if (!empty($array) && is_array($array) && count($array) > 0) {
				for($count = 0; $count <= count($array); $count ++) {
					self::required($array[$count]);
				}
			} else {
				die("A required value was empty." . self::$redirect);
			}
		}
	}