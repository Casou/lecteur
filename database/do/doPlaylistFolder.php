<?php

class PlaylistFolder {
	
	public $id;
	public $nom;
	public $id_user;
	
	public static function getTableName() {
		return "lct_playlist_folder";
	}
	
}

?>