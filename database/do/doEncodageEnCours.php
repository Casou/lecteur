<?php

class EncodageEnCours {
	
	public $nom_video;
	public $debut_encodage;
	public $bloquant;
	public $etat;
	
	public static function getTableName() {
		return "lct_encodage_en_cours";
	}
	
}