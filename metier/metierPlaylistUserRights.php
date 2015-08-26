<?php

class MetierPlaylistUserRights {
	
	public static function getPlaylistDTOSharedWithUser($id_user) {
		$query = "select p.* from ".Playlist::getTableName()." p ".
			" inner join ".PlaylistUserRights::getTableName()." pur on p.id = pur.id_playlist ".
			" where pur.id_user = $id_user and type_playlist = 'PLAYLIST' and pur.can_read = 1 ".
			" order by p.id_user ASC";
		$object = "Playlist";
		if (MetierUser::isUserAdmin($id_user)) {
			$query = "select * from ".Playlist::getTableName()." where id_user != $id_user order by id_user ASC";
		}
		$dtoArray = array();
		$playlists = Database::getResultsObjects($query, $object);
		
		foreach($playlists as $playlist) {
			 $dto = MetierPlaylistUserRights::getPlaylistDTO($id_user, $playlist);
			 $dto->playlist = $playlist;
			 $dtoArray[] = $dto;
		}
		
		return $dtoArray;
	}
	
	public static function getFolderDTOSharedWithUser($id_user) {
		$query = "select f.* from ".PlaylistFolder::getTableName()." f ".
				" inner join ".PlaylistUserRights::getTableName()." pur on f.id = pur.id_playlist ".
				" where pur.id_user = $id_user and pur.type_playlist = 'FOLDER' and pur.can_read = 1 ".
				" order by f.id_user ASC";
		if (MetierUser::isUserAdmin($id_user)) {
			$query = "select * from ".PlaylistFolder::getTableName()." where id_user != $id_user order by id_user ASC";
		}
		$dtoArray = array();
		$playlists = Database::getResultsObjects($query, "PlaylistFolder");
		
		foreach($playlists as $playlist) {
			$playlist->id_folder = null;
			$dto = MetierPlaylistUserRights::getPlaylistDTO($id_user, $playlist, 'FOLDER');
			$dto->playlist = $playlist;
			$dtoArray[] = $dto;
		}
		return $dtoArray;
	}
	
	public static function getPlaylistDTO($id_user, $playlist, $type_playlist = 'PLAYLIST') {
		$dto = new PlaylistDTO();
		// Si la playlist appartient à l'utilisateur connecté ou si c'est un admin, il a tous les droits
		if ($playlist->id_user == $id_user || MetierUser::isUserAdmin($id_user)) {
			$dto->can_read = true;
			$dto->can_read_plus = true;
			$dto->can_write = true;
			$dto->can_share = true;
		} else {
			
			// Les droits du folder prévalent sur les droits de la playlist
			if ($playlist->id_folder != null) {
				$playlistRightDTO = Database::getResultsObjects("select pur.* from ".PlaylistUserRights::getTableName()." pur ".
						" where pur.id_user = $id_user AND type_playlist = 'FOLDER' and pur.id_playlist = $playlist->id_folder", "PlaylistDTO");
			} else {
				$playlistRightDTO = Database::getResultsObjects("select pur.* from ".PlaylistUserRights::getTableName()." pur ".
						" where pur.id_user = $id_user and type_playlist = '$type_playlist' and pur.id_playlist = $playlist->id", "PlaylistDTO");
			}
			
			if (count($playlistRightDTO) > 0) {
				$playlistRightDTO = $playlistRightDTO[0];
			} else {
				$playlistRightDTO = new PlaylistDTO();
			}
			
			$dto->can_read = ($playlistRightDTO->can_read == 1);
			$dto->can_read_plus = ($playlistRightDTO->can_read_plus == 1);
			$dto->can_write = ($playlistRightDTO->can_write == 1);
			$dto->can_share = ($playlistRightDTO->can_share == 1);
		}
		
		$dto->nbVideos = count(MetierPlaylist::getVideoForPlaylist($playlist->id, $dto->can_read_plus));
		$user = MetierUser::getUserById($playlist->id_user);
		$dto->creator = $user->user->login;
		
		return $dto;
	}
	
	public static function getUserRights($id_playlist, $type_playlist = 'PLAYLIST') {
		$userArray = array();
		$playlistRights = Database::getResultsObjects("select pur.* from ".PlaylistUserRights::getTableName()." pur ".
				" where pur.id_playlist = $id_playlist and type_playlist = '$type_playlist'", "PlaylistUserRights");
		
		foreach ($playlistRights as $playlistRight) {
			$userArray[$playlistRight->id_user] = $playlistRight;
		}
		
		return $userArray;
	}
	
}

?>