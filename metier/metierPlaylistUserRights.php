<?php

class MetierPlaylistUserRights {
	
	public static function getPlaylistSharedWithUser($id_user) {
		$query = "select p.* from ".Playlist::getTableName()." p ".
			" inner join ".PlaylistUserRights::getTableName()." pur on p.id = pur.id_playlist ".
			" where pur.id_user = $id_user and pur.can_read = 1 ".
			" order by p.id_user ASC";
		if (MetierUser::isUserAdmin($id_user)) {
			$query = "select * from ".Playlist::getTableName()." where id_user != $id_user order by nom ASC";
		}
		$dtoArray = array();
		$playlists = Database::getResultsObjects($query, "Playlist");
		
		foreach($playlists as $playlist) {
			 $dto = MetierPlaylistUserRights::getPlaylistDTO($id_user, $playlist);
			 $dto->playlist = $playlist;
			 $dtoArray[] = $dto;
		}
		
		return $dtoArray;
	}
	
	public static function getPlaylistDTO($id_user, $playlist) {
		$dto = new PlaylistDTO();
		// Si la playlist appartient à l'utilisateur connecté ou si c'est un admin, il a tous les droits
		if ($playlist->id_user == $id_user || MetierUser::isUserAdmin($id_user)) {
			$dto->can_read = true;
			$dto->can_read_plus = true;
			$dto->can_write = true;
			$dto->can_share = true;
		} else {
			$playlistRightDTO = Database::getResultsObjects("select pur.* from ".PlaylistUserRights::getTableName()." pur ".
				" where pur.id_user = $id_user and pur.id_playlist = $playlist->id", "PlaylistDTO");
			
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
	
	public static function getUserRights($id_playlist) {
		$userArray = array();
		$playlistRights = Database::getResultsObjects("select pur.* from ".PlaylistUserRights::getTableName()." pur ".
				" where pur.id_playlist = $id_playlist", "PlaylistUserRights");
		
		foreach ($playlistRights as $playlistRight) {
			$userArray[$playlistRight->id_user] = $playlistRight;
		}
		
		return $userArray;
	}
	
}

?>