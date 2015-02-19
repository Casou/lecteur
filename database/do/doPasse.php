<?php

class Passe {
	
	public $id;
	public $nom;
	public $niveau;
	public $timer_debut;
	public $timer_fin;
	public $id_video;
	
	public static function getTableName() {
		return "lct_passe";
	}
	
}