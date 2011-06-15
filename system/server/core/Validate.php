<?php
/*
 * This class will validate user input prior to entry to a database:
 *  - required: The most simple form of validation, which ensures a value has been provided
 *  - numeric: Check to see if all of the supplied values are numeric
 *  - isArray: Validate all the values of an array, and optionally provide a targeted size
 *  - isUploaded: Check to see if a file was uploaded
*/

class Validate {
	private static $redirect = " Click <a href=\"javascript:window.location = document.location.href\">here</a> to retry.";
	
//The most simple form of validation, which ensures a value has been provided
	public static function required($string, $matches = false, $sizeSmall = false, $sizeLarge = false, $sizeEquals = false) {
		if (!empty($string)) {
			return $string;
		} else {
			die("A required value was empty." . self::$redirect);
		}
	}
	
//Check to see if all of the supplied values are numeric
	public static function numeric($string) {
		if (self::required($string) && is_numeric($string)) {
			return $string;
		} else {
			die("A required numeric value was empty." . self::$redirect);
		}
	}
	
//Validate all the values of an array, and optionally provide a targeted size
	public static function isArray($array, $size = false) {
		if (!empty($array) && is_array($array) && count($array) == $size) {
			$return = array();
			
			for($count = 0; $count <= count($array); $count ++) {
				array_push($return, self::required($array[$count]));
			}
			
			return $return;
		} else {
			die("A required value was empty." . self::$redirect);
		}
	}
	
//Check to see if a file was uploaded
	public static function isUploaded($file) {
		if (is_uploaded_file($_FILES[$file]['tmp_name'])) {
			return true;
		} else {
			die("A required file was not uploaded." . self::$redirect);
		}
	}
}
?>