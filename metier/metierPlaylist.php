<?php

class MetierPlaylist {
	
	public static function getAllPlaylist() {
		return Database::getResultsObjects("select * from ".Playlist::getTableName()." ORDER BY nom ASC;", "Playlist");
	}
	
	public static function getAllPlaylistAllowed($id_user) {
		if (MetierUser::isUserAdmin($id_user)) {
			return MetierPlaylist::getAllPlaylist();
		}
		
		// Playlist créées par l'utilisateur + playlist qu'on peut modifier
		$sql = "select * from ".Playlist::getTableName()." where id_user = $id_user ".
			" UNION ".
			" select p.* from ".Playlist::getTableName()." p ".
			" inner join ".PlaylistUserRights::getTableName()." pur on p.id = pur.id_playlist ".
			" where pur.id_user = $id_user and type_playlist = 'PLAYLIST' and pur.can_write = 1 ".
			" ORDER BY nom ASC;";
		return Database::getResultsObjects($sql, "Playlist");
	}
	
	public static function getPlaylistCreatedByUser($id_user, $id_folder = null) {
		if ($id_folder == null) {
			$sql = "select * from ".Playlist::getTableName()." where id_user = $id_user and id_folder is null order by nom ASC";
		} else {
			$sql = "select * from ".Playlist::getTableName()." where id_user = $id_user and id_folder = $id_folder order by nom ASC";
		}
		return Database::getResultsObjects($sql, "Playlist");
	}
	
	public static function getPlaylistDTONotCreatedByUserAndFolder($id_user, $id_folder) {
		$sql = "select * from ".Playlist::getTableName()." where id_user != $id_user and id_folder = $id_folder order by nom ASC";
		return Database::getResultsObjects($sql, "Playlist");
	}
	
	public static function getPlaylistDTOCreatedByUser($id_user, $id_folder = null) {
		if ($id_folder == null) {
			$sql = "select * from ".Playlist::getTableName()." where id_user = $id_user and id_folder is null order by nom ASC";
		} else {
			$sql = "select * from ".Playlist::getTableName()." where id_user = $id_user and id_folder = $id_folder order by nom ASC";
		}
		
		return MetierPlaylist::buildPlaylistDTOFromSql($id_user, $sql);
	}
	
	public static function getPlaylistDTONotCreatedByUser($id_user, $id_folder) {
		$sql = "select * from ".Playlist::getTableName()." where id_user != $id_user and id_folder = $id_folder order by nom ASC";
		return MetierPlaylist::buildPlaylistDTOFromSql($id_user, $sql);
	}
	
	private static function buildPlaylistDTOFromSql($id_user, $sql) {
		$playlists = Database::getResultsObjects($sql, "Playlist");
		$dtos = array();
		foreach ($playlists as $playlist) {
			
			$dto = MetierPlaylistUserRights::getPlaylistDTO($id_user, $playlist);
			/*
			$dto = new PlaylistDTO();
			$dto->can_read = true;
			$dto->can_read_plus = true;
			$dto->can_share = true;
			$dto->can_write = true;
				
			$dto->creator = null;
			$dto->nbVideos = count(MetierPlaylist::getVideoForPlaylist($playlist->id, $dto->can_read_plus));
			*/
			$dto->playlist = $playlist;
				
			$dtos[] = $dto;
		}
			
		return $dtos;
	}
	
	public static function getPlaylistForVideo($id_video) {
		$sql = "select p.* from ".Playlist::getTableName()." p ".
			" inner join ".Playlist::getJoinVideoTableName()." pv on pv.id_playlist = p.id ".
			" where pv.id_video = $id_video";
		return Database::getResultsObjects($sql, "Playlist");
	}
	
	public static function getPlaylistWithVideo($id_user, $id_playlist) {
		$pl = Database::getResultsObjects("select * from ".Playlist::getTableName()." where id = $id_playlist", "Playlist");
		$pl = $pl[0];
		$dto = MetierPlaylistUserRights::getPlaylistDTO($id_user, $pl);
		$dto->playlist = $pl;
		$dto->videos = MetierPlaylist::getVideoForPlaylist($id_playlist, $dto->can_read_plus);
		
		return $dto;
	}
	
	
	public static function getVideoForPlaylist($id_playlist, $skip_rights = false) {
		$array_dto = array();
		$query = 
			"select v.* from ".Video::getTableName()." v ".
			" inner join ".Playlist::getJoinVideoTableName()." pv on pv.id_video = v.id ";
		if (!$skip_rights) {
			$query .= " inner join ".Video::getJoinAllowedTableName().
				" allw on pv.id_video = allw.id_video and allw.id_user = ".CONNECTED_USER_ID." ";
		}
		$query .=
			" where pv.id_playlist = $id_playlist ".
			" order by pv.ordre asc";
		
		$array_do = Database::getResultsObjects($query, "Video");
		
		foreach($array_do as $do) {
			$dto = new VideoDTO();
			$dto->video = $do;
			if ($do->id_evenement != null) {
				$dto->evenement = MetierEvenement::getEvenementById($do->id_evenement);
			}
			$array_dto[] = $dto;
		}
		
		return $array_dto;
	}
	
	
	public static function savePlaylistPreferences($formulaire) {
		$formulaire = parse_str($formulaire);
		$hasTransaction = Database::beginTransaction();
		
		if (isset($idvideo) && count($idvideo) > 0) {
			$order = 1;
			foreach ($idvideo as $id) {
				$sql = "UPDATE ".Playlist::getJoinVideoTableName()." SET ordre = $order ".
					" WHERE id_playlist = $id_playlist and id_video = $id";
				Database::executeUpdate($sql);
				$order++;
			}
		}
		
		Database::executeUpdate("DELETE FROM ".PlaylistUserRights::getTableName()." WHERE id_playlist = $id_playlist AND type_playlist = 'PLAYLIST'");
		if (isset($id_user) && count($id_user) > 0) {
			foreach ($id_user as $id) {
				$readVariable = 'read_check_'.$id;
				$readPlusVariable = 'read_plus_check_'.$id;
				$writeVariable = 'write_check_'.$id;
				$shareVariable = 'share_check_'.$id;
				
				$read = (isset($$readVariable)) ? 1 : 0;
				$read_plus = (isset($$readPlusVariable)) ? 1 : 0;
				$write = (isset($$writeVariable)) ? 1 : 0;
				$share = (isset($$shareVariable)) ? 1 : 0;
				
				if (($read + $write + $share) > 0) {
					$sql = "INSERT INTO ".PlaylistUserRights::getTableName()." (id_playlist, type_playlist, id_user, can_read, can_read_plus, can_write, can_share)".
						" VALUES ($id_playlist, 'PLAYLIST', $id, $read, $read_plus, $write, $share)";
					Database::executeUpdate($sql);
				}
			}
		}
		
		if ($hasTransaction) Database::commit();
	}
	
	public static function linkVideoPlaylist($id_video, $playlists) {
		$hasTransaction = Database::beginTransaction();
		Database::executeUpdate("DELETE FROM ".Playlist::getJoinVideoTableName()." WHERE id_video=$id_video");
		
		foreach($playlists as $playlist) {
			$sql = "INSERT INTO ".Playlist::getJoinVideoTableName()." (id_video, id_playlist) ".
				" VALUES ($id_video, $playlist)";
			Database::executeUpdate($sql);
		}
		if ($hasTransaction) Database::commit();
	}
	
	
	
	public static function insertPlaylist($name, $id_user) {
		$newId = Database::getMaxId(Playlist::getTableName()) + 1;
		
		$sql = "INSERT INTO ".Playlist::getTableName()."(id, nom, id_user) ".
			"VALUES ($newId, '".escapeString($name)."', $id_user);";
		Database::executeUpdate($sql);
		
		return $newId;
	}
	
	public static function saveVideoToPlaylist($id_playlist, $nom_playlist, $ids_video) {
		$hasTransaction = Database::beginTransaction();
		
		if ($nom_playlist != "") {
			$id_playlist = MetierPlaylist::insertPlaylist($nom_playlist, CONNECTED_USER_ID);
		}
		
		foreach ($ids_video as $id_video) {
			$sql = "INSERT INTO ".Playlist::getJoinVideoTableName()."(id_video, id_playlist) ".
				"VALUES ($id_video, $id_playlist)";
			Database::executeUpdate($sql);
		}
		
		if ($hasTransaction) Database::commit();
	}
	
	
	public static function deleteVideoFromPlaylist($id_playlist, $id_video) {
		$sql = "DELETE FROM ".Playlist::getJoinVideoTableName().
			" WHERE id_video = $id_video AND id_playlist = $id_playlist";
		Database::executeUpdate($sql);
	}
	
	public static function deletePlaylist($id_playlist) {
		$hasTransaction = Database::beginTransaction();
		
		Database::executeUpdate("DELETE FROM ".Playlist::getJoinVideoTableName()." WHERE id_playlist = $id_playlist");
		Database::executeUpdate("DELETE FROM ".PlaylistUserRights::getTableName()." WHERE id_playlist = $id_playlist and type_playlist = 'PLAYLIST'");
		Database::executeUpdate("DELETE FROM ".Playlist::getTableName()." WHERE id = $id_playlist");
		
		if ($hasTransaction) Database::commit();
	}
		
}

?>