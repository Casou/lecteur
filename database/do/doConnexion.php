<?php

class Connexion {
	
	public $date;
	public $login;
	public $ip;
	
	public static function getTableName() {
		return "lct_connexion";
	}
	
}