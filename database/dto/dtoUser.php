<?php

class UserDTO {
	
	public $user;
	public $droits;
	public $droitsNom;
	public $profils;

	public function __construct() {
		$this->droitsNom = array();
		$this->profils = array();
	}
}

?>