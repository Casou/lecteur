<?php

class PlaylistUserRights {
	
	public $id_playlist;
	public $id_user;
	public $can_read;
	public $can_read_plus;
	public $can_write;
	public $can_share; 
	public $type_playlist; 
	
	public static function getTableName() {
		return "lct_playlist_user_rights";
	}
	
}

?>