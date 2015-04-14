<?php

class User {
	
	public $id;
	public $login;
	public $password;
	public $log_level;
	
	public static function getTableName() {
		return "lct_user";
	}
	
	public static function getJoinProfilTableName() {
		return "lct_user_profil";
	}
	
	
}