<?php

class Playlist {
	
	public $id;
	public $nom;
	public $id_user; // Propriétaire
	public $id_folder;
	
	public static function getTableName() {
		return "lct_playlist";
	}
	
	public static function getJoinVideoTableName() {
		return "lct_playlist_video";
	}
	
}

?>