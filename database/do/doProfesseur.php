<?php

class Professeur {
	
	public $id;
	public $nom;
	
	public static function getTableName() {
		return "lct_professeur";
	}
	
	public static function getJoinVideoTableName() {
		return "lct_video_professeur";
	}
	
}