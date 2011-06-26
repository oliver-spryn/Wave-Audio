<?php
/*
 * This is part of an essential package of the system's core, the "constructor"
 * package.
 * 
 * The only attributes of this package that are not consistant with the HTML
 * attritues it will be creating are the "dbValue" and "additionalAttr" attributes.
 * "dbValue" variable checks to see if a given variable exists, and if it does,
 * it is placed as the value of the input control. This variable is best used when
 * checking to see if PHP pulled a given value from a database. Don't confuse this
 * variable with "value", which is intended to contain a static, non-database driven
 * string, such as a default value. If both, "dbValue" and "value" have a value,
 * then "dbValue" will take priority. "additionalAttr" allows additional attributes, 
 * such as JavaScript events, to be added to an input control.
 * 
 * This class creates and manages all of the possible attributes and properties
 * that can be used to create and manage a standard text input control.
*/

class TextInput extends InputBase {
//These properties are reqiured, and are defined on an as-needed basis
	public $name;
	
//These properties are optional, but can be defined on an as-needed basis
	public $class = false;
	public $dbValue = false;
	public $disabled = false;
	public $id = false; 
	public $maxLength = false;
	public $readOnly = false;
	public $size = false;
	public $value = false;
	public $additionalAttr = false;
	
//New to HTML5, jQuery will provide fallback if no HTML5 is supported
	public $autoComplete = false;
	public $autoFocus = false;
	public $list = false;
	public $max = false;
	public $min = false;
	public $multiple = false; //Works for "email" type only
	public $pattern = false;
	public $placeHolder = false;
	public $required = false;
	public $step = false;
	
	public function __construct() {
	//Nothing to do!
	}
	
//Parse and build the suggestion list
	private function parseList() {
		$listItems = "\n<datalist id=\"list_" . $this->name . "\">\n";
		
		foreach($this->list as $label => $value) {
			$listItems .= "<option label=\"" . $label . "\" value=\"" . $value . "\">\n";
		}
		
		$listItems .= "</datalist>";
		
		return $listItems;
	}
	
//Build the text input control
	public function build() {
	//Which value should be provided?
		if ($this->dbValue && isset($$this->dbValue)) {
			$value = $$this->dbValue;
		} elseif ($this->value) {
			$value = $this->value;
		} else {
			$value = false;
		}
		
		$return = "<input type=\"text\" name=\"" . $this->name . "\"";
		$return .= $this->disabled ? " disabled=\"disabled\"" : "";
		$return .= $this->id ? " id=\"" . $this->id . "\"" : "";
		$return .= $this->maxLength ? " maxlength=\"" . $this->maxLength . "\"" : "";
		$return .= $this->readOnly ? " readonly=\"readonly\"" : "";
		$return .= $this->size ? " size=\"" . $this->size . "\"" : "";
		$return .= $value ? " value=\"" . $value . "\"" : "";
		
	//Begin new HTML5 attributes
		$return .= !$this->autoComplete ? " autocomplete=\"off\"" : ""; //This has been supported for years, now just standardized
		$return .= $this->autoFocus ? " autofocus=\"autofocus\"" : "";
		$return .= $this->list && is_array($this->list) && !empty($this->list) ? " list=\"list_" . $this->name . "\"" : "";
		$return .= $this->max ? " max=\"" . $this->max . "\"" : "";
		$return .= $this->min ? " min=\"" . $this->min . "\"" : "";
		$return .= $this->multiple ? " multiple=\"multiple\"" : "";
		$return .= $this->pattern ? " pattern=\"" . $this->pattern . "\"" : "";
		$return .= $this->placeHolder ? " placeholder=\"" . $this->placeholder . "\"" : "";
		$return .= $this->required ? " required=\"required\"" : "";
		$return .= $this->step ? " step=\"" . $this->step . "\"" : "";
		
	//Apply additional attributes at the end
		$return .= $this->additionalAttr ? " " . $this->additionalAttr : "";
		
	//Finish building the element
		$return .= " />";
		
	//Parse and build the suggestion list
		$return .= $this->list && is_array($this->list) && !empty($this->list) ? $this->parseList() : "";
		
		return $return;
	}
}