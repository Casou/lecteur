<?php

class Droit {
	
	public $id;
	public $nom;
	public $label;
	public $id_droit_categorie;
	public $ordre;
	
	public static function getTableName() {
		return "lct_droit";
	}
	
	public static function getJoinUserTableName() {
		return "lct_user_droit";
	}
	
}