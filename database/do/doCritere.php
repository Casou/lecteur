<?php

class Critere {
	
	public $id;
	public $id_profil;
	public $types_video;
	public $danses;
	public $tags;
	public $evenements;
	
	public function __construct() {
		$this->types_video = "";
		$this->danses = "";
		$this->tags = "";
		$this->evenements = "";
	}
	
	public static function getTableName() {
		return "lct_profil_critere";
	}
	
}