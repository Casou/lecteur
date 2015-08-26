<?php

class MetierPlaylistFolder {
	
	public static function getPlaylistFolderById($id) {
		return Database::getResultsObjects("select * from ".PlaylistFolder::getTableName()." WHERE id = $id", "PlaylistFolder");
	}
	
	
	public static function getAllPlaylistFolder() {
		return Database::getResultsObjects("select * from ".PlaylistFolder::getTableName()." ORDER BY nom ASC", "PlaylistFolder");
	}
	
	public static function getPlaylistFolderCreatedByUser($id_user) {
		$results = Database::getResultsObjects("select * from ".PlaylistFolder::getTableName()." where id_user = $id_user order by nom ASC", "PlaylistFolder");
		$dtoArray = array();
		
		foreach ($results as $playlist) {
			$dto = MetierPlaylistUserRights::getPlaylistDTO($id_user, $playlist, 'FOLDER');
			$dto->playlist = $playlist;
			$dto->creator = null;
			$dtoArray[] = $dto;
		}
		return $dtoArray;
	}
	
	public static function savePlaylistFolder($id, $nom, $id_user) {
		if ($id == null) {
			$sql = "INSERT INTO ".PlaylistFolder::getTableName()." (nom, id_user) VALUES ('".escapeString($nom)."', $id_user)";
		} else {
			$sql = "UPDATE ".PlaylistFolder::getTableName()." set nom = '".escapeString($nom)."' where id_user = $id_user";
		}
		Database::executeUpdate($sql);
	}
	
	public static function savePlaylistFolderItem($id_folder, $id_playlist) {
		if ($id_folder != null) {
			$sql = "UPDATE ".Playlist::getTableName()." SET id_folder = $id_folder where id = $id_playlist";
			Database::executeUpdate($sql);
		} else {
			$sql = "UPDATE ".Playlist::getTableName()." SET id_folder null where id = $id_playlist";
			Database::executeUpdate($sql);
		}
	}
	
	
	
	public static function saveFolderPreferences($formulaire) {
		$formulaire = parse_str($formulaire);
		$hasTransaction = Database::beginTransaction();
		
		Database::executeUpdate("UPDATE ".PlaylistFolder::getTableName()." SET nom = '".Fwk::escapeSimpleQuote($nom_folder)."' WHERE id = $id_folder");
	
		Database::executeUpdate("DELETE FROM ".PlaylistUserRights::getTableName()." WHERE id_playlist = $id_folder AND type_playlist = 'FOLDER'");
		if (isset($id_user) && count($id_user) > 0) {
			foreach ($id_user as $id) {
				$readVariable = 'read_check_'.$id;
				$readPlusVariable = 'read_plus_check_'.$id;
	
				$read = (isset($$readVariable)) ? 1 : 0;
				$read_plus = (isset($$readPlusVariable)) ? 1 : 0;
				$write = 0;
				$share = 0;
	
				if (($read + $write + $share) > 0) {
					$sql = "INSERT INTO ".PlaylistUserRights::getTableName()." (id_playlist, type_playlist, id_user, can_read, can_read_plus, can_write, can_share)".
							" VALUES ($id_folder, 'FOLDER', $id, $read, $read_plus, $write, $share)";
					Database::executeUpdate($sql);
				}
			}
		}
	
		if ($hasTransaction) Database::commit();
	}
	
	
	
	public static function deleteFolder($id_folder) {
		$sql = "UPDATE ".Playlist::getTableName()." SET id_folder = null where id_folder = $id_folder";
		Database::executeUpdate($sql);
		
		$sql = "DELETE FROM ".PlaylistFolder::getTableName()." WHERE id = $id_folder";
		Database::executeUpdate($sql);
	}
	
}

?>