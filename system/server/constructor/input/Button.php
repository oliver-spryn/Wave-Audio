<?php
/*
 * This is part of an essential package of the system's core, the "constructor"
 * package.
 * 
 * This class creates and manages all of the possible attributes and properties
 * that can be used to create and manage a standard button control.
*/

class Button {
//These properties are reqiured, and are defined on an as-needed basis
	public $name;
	
//These properties are optional, but can be defined on an as-needed basis
	public $class = false;
	public $disabled = false;
	public $id = false; 
	public $maxLength = false;
	public $readOnly = false;
	public $size = false;
	public $value = false;
	public $additionalAttr = false;
	
}
?>