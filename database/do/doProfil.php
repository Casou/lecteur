<?php

class Profil {
	
	public $id;
	public $nom;
	public $is_admin;
	
	public static function getTableName() {
		return "lct_profil";
	}
	
	public static function getJoinCritereTableName() {
		return "lct_video_favori";
	}
	
}