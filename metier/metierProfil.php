<?php

class MetierProfil {
	
	public static function getAllProfil() {
		return Database::getResultsObjects("select * from ".Profil::getTableName()."  order by nom asc", "Profil");
	}
	
	public static function getProfilById($id) {
		$sql = "select * from ".Profil::getTableName()." where id = $id";
		$results = Database::getResultsObjects($sql, "Profil");
		if (count($results) == 0) {
			return null;
		}
		$profil = $results[0];
		
		$dto = new ProfilDTO();
		$dto->profil = $profil;
		$dto->criteres = MetierCritere::getCritereByProfil($id);
		
		return $dto;
	}
	
	public static function getProfilByUser($id_user) {
		$sql = "select distinct p.* from ".Profil::getTableName()." p ".
			" inner join ".User::getJoinProfilTableName()." up on p.id = up.id_profil ".
			" where up.id_user = $id_user order by p.nom asc";
		return Database::getResultsObjects($sql, "Profil");
	}
	
	
	public static function getProfilesAllowedForVideo($ids_video, $buildObjects = false) {
		$ids = "";
		foreach($ids_video as $id) {
			if ($ids != "") {
				$ids .= ', ';
			}
			$ids .= $id;
		}
		$sql = "select id_profil, count(*) as compte from ".Video::getJoinAllowedManualToProfileTableName().
			" where id_video in ($ids) ".
			" group by id_profil";
		$results = Database::getResults($sql);
		$ids_profil = array();
		foreach($results as $result) {
			// On ne renvoie que les utilisateurs qui dont toutes les vidéos sont associées
			if ($result['compte'] == count($ids_video)) {
				if (!$buildObjects) {
					$ids_profil[] = $result['id_profil'];
				} else {
					$ids_profil[] = MetierProfil::getProfilById($result['id_profil']);
				}
			}
		}
		
		return $ids_profil;
	}
	
	public static function getProfilByCritere($critere) {
		$where = "";
		$whereClause = false;
		
		
		$where .= " ( ";
		if ($critere->types_video != null && $critere->types_video != "") {
			$types = explode(";", $critere->types_video);
			foreach($types as $type) {
				$where .= " types_video like '$type;%' or types_video like '%;$type' or types_video = '$type' ";
				$where .= " or ";	
			}
			$whereClause = true;
		}
		$where .= " types_video is null )";
		$where .= " AND ";
		
		
		$where .= " ( ";
		if ($critere->danses != null && $critere->danses != "") {
			$danses = explode(";", $critere->danses);
			foreach($danses as $id_danse) {
				$where .= " danses like '$id_danse;%' or danses like '%;$id_danse' or danses = '$id_danse' ";
				$where .= " or ";	
			}
			$whereClause = true;
		}
		$where .= " danses is null ) ";
		$where .= " AND ";
		
		
		$where .= " ( ";
		if ($critere->tags != null && $critere->tags != "") {
			$tags = explode(";", $critere->tags);
			foreach($tags as $id_tag) {
				$where .= " tags like '$id_tag;%' or tags like '%;$id_tag' or tags = '$id_tag' ";
				$where .= " or ";	
			}
			$whereClause = true;
		}
		$where .= " tags is null ) ";
		$where .= " AND ";
		
		
		$where .= " ( ";
		if ($critere->evenements != null && $critere->evenements != "") {
			$evenements = explode(";", $critere->evenements);
			foreach($evenements as $id_evenement) {
				$where .= " evenements like '$id_evenement;%' or evenements like '%;$id_evenement' or evenements = '$id_evenement' ";
				$where .= " or ";	
			}
			$whereClause = true;
		}
		$where .= " evenements is null ) ";
		$where .= " AND ";
		
		// Si aucune info n'a été saisie pour la vidéo, on ne renvoie que les admins
		if (!$whereClause) {
			$sql = "select * from ".Profil::getTableName()." where is_admin = 1";
			return Database::getResultsObjects($sql, "Profil");
		}
		
		$sql = "select distinct p.* from ".Profil::getTableName()." p ".
			" inner join ".Critere::getTableName()." crt on p.id = crt.id_profil ".
			" where $where 1=1 ".
			" UNION ".
			" select * from ".Profil::getTableName()." where is_admin = 1";
		//echo $sql;
		return Database::getResultsObjects($sql, "Profil");
	}
	
	
	
	
	
	
	public static function saveProfilsAllowedForVideo($ids_video, $profils, $delete_existing = true) {
		$hasTransaction = Database::beginTransaction();
		
		$in_clause_video = "";
		foreach($ids_video as $id_video) {
			if ($in_clause_video != "") {
				$in_clause_video .= ", ";
			}
			$in_clause_video .= $id_video;
		}
		
		if ($delete_existing) {
			$profils_allowed = MetierProfil::getProfilesAllowedForVideo($ids_video);
			if ($profils_allowed != null) {
				$in_clause_profile = "";
				foreach($profils_allowed as $profil) {
					if ($in_clause_profile != "") {
						$in_clause_profile .= ", ";
					}
					$in_clause_profile .= $profil;
				}
				
				$sql = "DELETE FROM ".Video::getJoinAllowedManualToProfileTableName()." where id_video in ($in_clause_video) and id_profil in ($in_clause_profile)";
				Database::executeUpdate($sql);
			}
		}
			
		if ($profils != null) {
			foreach($profils as $profil) {
				foreach($ids_video as $id_video) {
					$sql = "DELETE FROM ".Video::getJoinAllowedManualToProfileTableName()." where id_video = $id_video and id_profil = $profil";
					Database::executeUpdate($sql);
				
					$sql = "INSERT INTO ".Video::getJoinAllowedManualToProfileTableName()." (id_video, id_profil) values ($id_video, $profil)";
					Database::executeUpdate($sql);
				}
			}
		}
		
		if ($hasTransaction) Database::commit();
	}
	
	
	public static function saveProfil() {
		$id = $_POST['id'];
		$nom = stripslashes($_POST['nom']);
		$criteres = isset($_POST['criteres']) ? $_POST['criteres'] : array();
		$is_admin = $_POST['is_admin'] == "true" ? 1 : 0; 
		
		$hasTransaction = Database::beginTransaction();
		
		if ($id == '') {
			
			$sql = "INSERT INTO ".Profil::getTableName()." (nom, is_admin) VALUES ('".escapeString($nom)."', $is_admin)";
			Database::executeUpdate($sql);
			$id = Database::getMaxId(Profil::getTableName());
			
		} else {
	
			$sql = "UPDATE ".Profil::getTableName()." SET nom='".escapeString($nom)."', is_admin = $is_admin ";
			$sql .= " WHERE id=$id";
			Database::executeUpdate($sql);
			
			$sql = "DELETE FROM ".Critere::getTableName()." WHERE id_Profil=$id";
			Database::executeUpdate($sql);
			
		}
		
		if (count($criteres) > 0 && !$is_admin) {
			foreach ($criteres as $critere) {
				$ds = "null";
				if (isset($critere['danses'])) {
					$ds = "'";
					foreach($critere['danses'] as $danse) {
						if ($ds != "'") { 
							$ds .= ";";
						}
						$ds .= $danse;
					}
					$ds .= "'";
				}
				
				$tp = "null";
				if (isset($critere['types'])) {
					$tp = "'";
					foreach($critere['types'] as $type) {
						if ($tp != "'") { 
							$tp .= ";";
						}
						$tp .= $type;
					}
					$tp .= "'";
				}
				
				$tg = "null";
				if (isset($critere['tags'])) {
					$tg = "'";
					foreach($critere['tags'] as $tag) {
						if ($tg != "'") { 
							$tg .= ";";
						}
						$tg .= $tag;
					}
					$tg .= "'";
				}
				
				$ev = "null";
				if (isset($critere['evenements'])) {
					$ev = "'";
					foreach($critere['evenements'] as $event) {
						if ($ev != "'") { 
							$ev .= ";";
						}
						$ev .= $event;
					}
					$ev .= "'";
				}
				
				
				$sql = "INSERT INTO ".Critere::getTableName()." (id_Profil, types_video, danses, tags, evenements) VALUES ($id, $tp, $ds, $tg, $ev)";
				Database::executeUpdate($sql);
			}
		}
		
		if ($hasTransaction) Database::commit();
		
		return $id;
	}
	
	public static function deleteProfil($id) {
		$hasTransaction = Database::beginTransaction();
		
		$sql = "DELETE FROM ".User::getJoinProfilTableName()." WHERE id_Profil = $id;";
		Database::executeUpdate($sql);
		
		$sql = "DELETE FROM ".Video::getJoinAllowedManualToProfileTableName()." WHERE id_Profil = $id";
		Database::executeUpdate($sql);
		
		$sql = "DELETE FROM ".Critere::getTableName()." WHERE id_profil = $id;";
		Database::executeUpdate($sql);
		
		$sql = "DELETE FROM ".Profil::getTableName()." WHERE id = $id;";
		Database::executeUpdate($sql);
		
		if ($hasTransaction) Database::commit();
	}
	
}

?>