<?php

class UserDTO {
	
	public $user;
	public $droits;
	public $droitsNom;
	public $profils;
	public $logConnexion;

	public function __construct() {
		$this->droitsNom = array();
		$this->profils = array();
	}
}

?>