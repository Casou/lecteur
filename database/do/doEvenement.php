<?php

class Evenement {
	
	public $id;
	public $nom;
	public $date;
	public $ville;
	public $couleur;
	
	public static function getTableName() {
		return "lct_evenement";
	}
	
	/*
	public static function getJoinVideoTableName() {
		return "lct_video_evenement";
	}
	*/
	
}