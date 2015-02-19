<?php

class Video {
	
	public $id;
	public $nom_video;
	public $nom_affiche;
	public $type;
	public $duree;
	public $date_insertion;
	public $code_partage;
	public $id_evenement;
	
	public static function getTableName() {
		return "lct_video";
	}
	
	public static function getJoinFavoriTableName() {
		return "lct_video_favori";
	}
	
	public static function getJoinAllowedManualTableName() {
		return "lct_allowed_video_user_manual";
	}
	
	public static function getJoinAllowedManualToProfileTableName() {
		return "lct_allowed_video_profil_manual";
	}
	
	public static function getJoinAllowedTableName() {
		return "lct_allowed_video_user_calc";
	}
	
}

?>