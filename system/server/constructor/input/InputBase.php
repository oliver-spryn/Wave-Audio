<?php
/*
 * This is part of an essential package of the system's core, the "constructor"
 * package.
 * 
 * This class is merely a back-end management class whose purpose is to create 
 * and establish instance variables and logic which will be used across all or 
 * nearly all of the constructor classes.
*/

class InputBase {
//These properties are reqiured, and are defined on an as-needed basis
	public $name;
	
//These properties are reqiured are not part of the global attributes, but they are common to input controls
	
	
//These properties are optional, and part of the global attributes
	public $accessKey = false;
	public $class = false;
	public $dir = false;
	public $id = false;
	public $lang = false;
	public $style = false;
	public $tabIndex = false;
	public $title = false;
	
//New to the HTML5 global attributes
	public $contentEditable = false;
	public $contextMenu = false;
	public $draggable = false;
	public $dropZone = false;
	public $hidden = false;
	public $spellCheck = false;
}