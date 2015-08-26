<?php

class Danse {
	
	public $id;
	public $nom;
	
	public static function getTableName() {
		return "lct_danse";
	}
	
	public static function getJoinVideoTableName() {
		return "lct_video_danse";
	}
	
	public static function getJoinUserTableName() {
		return "lct_user_danse";
	}
	
	public static function getJoinUserOrderTableName() {
		return "lct_user_danse_order";
	}
	
}