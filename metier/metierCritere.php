<?php

class MetierCritere {
	
	public static function getCritereByProfil($id_profil) {
		return Database::getResultsObjects("select * from ".Critere::getTableName()." where id_profil = $id_profil", "Critere");
	}
	
	public static function recoverCritereFromFormulaire($formulaire) {
		$critere = new Critere();
		parse_str($formulaire);
		
		if (isset($type)) {	$critere->types_video = arrayToCommaString($type);	}
		if (isset($id_evenement)) {	$critere->evenements = arrayToCommaString($id_evenement);	}
		if (isset($danse)) {	$critere->danses = arrayToCommaString($danse);	}
		if (isset($tag)) {	$critere->tags = arrayToCommaString($tag);	}
		
		return $critere;
	}
	
	
	public static function calculateAllowedVideosForAllUsers() {
		$users = MetierUser::getAllUser();
		foreach ($users as $user) {
			MetierCritere::calculateAllowedVideos($user->id);
		}
	}
	
	
	public static function calculateAllowedVideos($id_user = null) {
		if ($id_user == null) {
			$id_user = $_SESSION['userId'];
		}
		
		// Remise à zéro des vidéos
		Database::executeUpdate("DELETE FROM ".Video::getJoinAllowedTableName()." where id_user = $id_user");
		
		$profils = MetierProfil::getProfilByUser($id_user);
		foreach($profils as $profil) {
			Logger::warn("Profil : $profil->nom");
			if ($profil->is_admin) {
				MetierCritere::allowedAllVideos();
				continue;
			}
				
			$criteres = MetierCritere::getCritereByProfil($profil->id);
			foreach($criteres as $critere) {
				$where = "WHERE id not in (select id_video from lct_allowed_video_user_calc where id_user = $id_user) ";
			
				$where_tmp = "";
				$danses = $critere->danses == "" ? array() : explode(";", $critere->danses);
				$types = $critere->types_video == "" ? array() : explode(";", $critere->types_video);
				$tags = $critere->tags == "" ? array() : explode(";", $critere->tags);
				$evenements = $critere->evenements == "" ? array() : explode(";", $critere->evenements);
				
				if (count($danses) > 0) {
					$where_tmp .= "(";
					foreach($danses as $danse) {
						$where_tmp .= "vd.id_danse=".$danse." OR ";	
					}
					$where_tmp .= " 0=1)";
				}
				
				if (count($types) > 0) {
					if ($where_tmp != "") {
						$where_tmp .= " AND ";
					}
					
					$where_tmp .= "(";
					foreach($types as $type) {
						$where_tmp .= "v.type='$type' OR ";	
					}
					$where_tmp .= " 0=1)";
				}
				
				if (count($tags) > 0) {
					if ($where_tmp != "") {
						$where_tmp .= " AND ";
					}
					
					$where_tmp .= "(";
					foreach($tags as $tag) {
						$where_tmp .= " tv.id_tag=$tag OR ";	
					}
					$where_tmp .= " 0=1)";
				}
				
				if (count($evenements) > 0) {
					if ($where_tmp != "") {
						$where_tmp .= " AND ";
					}
					
					$where_tmp .= "(";
					foreach($evenements as $evenement) {
						$where_tmp .= " v.id_evenement=$evenement OR ";	
					}
					$where_tmp .= " 0=1)";
				}
				
				$where .= " AND ( $where_tmp ) ";
				
				$sql = "INSERT INTO ".Video::getJoinAllowedTableName()." (id_video, id_user) SELECT distinct id, $id_user".
					" from ".Video::getTableName()." v ".
					" INNER JOIN ".Danse::getJoinVideoTableName()." vd on vd.id_video = v.id ";
				if (count($tags) > 0) {
					$sql .= " INNER JOIN ".Tag::getJoinVideoTableName()." tv on tv.id_video = v.id ";
				}
					$sql .= $where;
				Database::executeUpdate($sql);
			}
		}
		
		// On ajoute les vidéos ajoutées à la main à des profils
		$sql = "INSERT INTO ".Video::getJoinAllowedTableName()." (id_video, id_user) SELECT id_video, $id_user".
					" from ".Video::getJoinAllowedManualToProfileTableName()." p ".
					" inner join ".User::getJoinProfilTableName()." usr on p.id_profil = usr.id_profil ". 
					" WHERE usr.id_user = $id_user".
					" and id_video not in (select id_video from lct_allowed_video_user_calc where id_user = $id_user)";
		Database::executeUpdate($sql);
		
		
		// On ajoute les vidéos ajoutées à la main à des utilisateurs
		$sql = "INSERT INTO ".Video::getJoinAllowedTableName()." (id_video, id_user) SELECT id_video, $id_user".
					" from ".Video::getJoinAllowedManualTableName()." usr ".
					" WHERE usr.id_user = $id_user". 
					" and id_video not in (select id_video from lct_allowed_video_user_calc where id_user = $id_user)";
		Database::executeUpdate($sql);
		
	}
	
	public static function allowedAllVideos($id_user = null) {
		if ($id_user == null) {
			$id_user = $_SESSION['userId'];
		}
		Logger::debug('CONNECTION ADMIN');
		Database::executeUpdate("DELETE FROM ".Video::getJoinAllowedTableName()." where id_user = ".$_SESSION['userId']);
		Database::executeUpdate("INSERT INTO ".Video::getJoinAllowedTableName()." (id_video, id_user) SELECT id, $id_user".
			" from ".Video::getTableName());
	}
	
}

?>