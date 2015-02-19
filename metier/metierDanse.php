<?php

class MetierDanse {
	
	public static function getAllDanse($skipSession = false) {
		if ($skipSession) {
			return Database::getResultsObjects("SELECT * FROM ".Danse::getTableName(), "Danse");
		}
		
		return Database::getResultsObjects(
				"SELECT * FROM ".Danse::getTableName()." d ".
				"INNER JOIN ".Danse::getJoinUserTableName()." ud ON ud.id_danse = d.id ".
				"WHERE id_user=".$_SESSION['userId'], 
				"Danse");
	}
	
	public static function getAllowedDanse() {
		return Database::getResultsObjects(
				"SELECT distinct d.* FROM ".Video::getJoinAllowedTableName()." allw_vid ".
				"INNER JOIN ".Danse::getJoinVideoTableName()." vd on vd.id_video = allw_vid.id_video ".
				"INNER JOIN ".Danse::getTableName()." d on d.id = vd.id_danse ".
				"WHERE id_user=".$_SESSION['userId'], 
				"Danse");
	}



	public static function getDanseById($id) {
		$sql = "select * from ".Danse::getTableName()." where id = $id";
		$results = Database::getResultsObjects($sql, "Danse");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getDanseByNom($nom) {
		$sql = "select * from ".Danse::getTableName()." where nom = '".escapeString($nom)."'";
		$results = Database::getResultsObjects($sql, "Danse");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getDanseByVideo($id_video) {
		$sql = "select d.* from ".Danse::getJoinVideoTableName()." dv ".
			" INNER JOIN ".Danse::getTableName()." d ON dv.id_danse = d.id ".	
			" where id_video = $id_video;";
		return Database::getResultsObjects($sql, "Danse");
	}
	
	public static function getDanseByEvenement($id_evenement) {
		$sql = "select distinct d.* from ".Danse::getTableName()." d ".
				" INNER JOIN ".Danse::getJoinVideoTableName()." dv ON dv.id_danse = d.id ".
				" INNER JOIN ".Video::getTableName()." v ON dv.id_video = v.id ".
				" INNER JOIN ".Evenement::getTableName()." e ON v.id_evenement = e.id ".
				" where e.id = $id_evenement";
		return Database::getResultsObjects($sql, "Danse");
	}
	
	public static function getDanseActivatedByEvenement($id_evenement, $id_user) {
		$sql = "select distinct d.* from ".Danse::getTableName()." d ".
				" INNER JOIN ".Danse::getJoinVideoTableName()." dv ON dv.id_danse = d.id ".
				" INNER JOIN ".Video::getTableName()." v ON dv.id_video = v.id ".
				" INNER JOIN ".Evenement::getTableName()." e ON v.id_evenement = e.id ".
				" INNER JOIN ".Danse::getJoinUserTableName()." ud on dv.id_danse = ud.id_danse ".
				" where e.id = $id_evenement and ud.id_user = $id_user";
		return Database::getResultsObjects($sql, "Danse");
	}
	
	public static function getDanseIdByVideo($id_video) {
		$sql = "select id_danse from ".Danse::getJoinVideoTableName()." where id_video = $id_video;";
		$results = Database::getResults($sql);
		$resultArray = array();
		foreach($results as $result) {
			$resultArray[] = $result['id_danse'];
		}
		return $resultArray;
	}
	
	
	public static function getDanseActivatedByUser($id_user) {
		$sql = "select d.* ".
					" from ".Danse::getTableName()." d ".
					" inner join ".Danse::getJoinUserTableName()." ud on ud.id_danse = d.id ".
					" where ud.id_user = $id_user";
		return Database::getResultsObjects($sql, "Danse");
	}
	
	public static function isDanseActivated($id_danse, $id_user) {
		$sql = "select id_danse from ".Danse::getJoinUserTableName().
			" where id_danse = $id_danse and id_user = $id_user";
		$results = Database::getResults($sql);
		return count($results) > 0;
	}
	
	public static function switchOn($id_user, $id_danse, $switchOn) {
		if ($switchOn) {
			$sql = "INSERT INTO ".Danse::getJoinUserTableName()." (id_user, id_danse) ".
				"VALUES ($id_user, $id_danse)";
		} else {
			$sql = "DELETE FROM ".Danse::getJoinUserTableName()." WHERE id_user=$id_user ".
					"AND id_danse=$id_danse";
		}
		$results = Database::executeUpdate($sql);
	}
	
	
	
	
	
	
	
	public static function insertDanse($nom) {
		if (trim($nom) == "") {
			throw new Exception("Le nom de la danse ne peut pas être vide.");
		}
		
		$danse = MetierDanse::getDanseByNom($nom);
		if ($danse != null) {
			throw new Exception("La danse $nom existe déjà.");	
		}
		
		$sql = "INSERT INTO ".Danse::getTableName()."(nom) VALUES  ('".escapeString($nom)."');";
		Database::executeUpdate($sql);
		
		$idDanse = Database::getMaxId(Danse::getTableName());
		$sql = "INSERT INTO ".Danse::getJoinUserTableName()." (id_user, id_danse) ".
				"SELECT id, $idDanse FROM ".User::getTableName();
		Database::executeUpdate($sql);
		
		return $idDanse;
	}
	
	public static function updateNomDanse($id, $nom) {
		if (trim($nom) == "") {
			throw new Exception("Le nom de la danse ne peut pas être vide.");
		}
	
		$danse = MetierDanse::getDanseById($id);
		if ($danse == null) {
			throw new Exception("La danse numéro $id n'existe pas en base.");
		}
	
		$sql = "UPDATE ".Danse::getTableName()." SET nom='".escapeString($nom)."' WHERE id=$id;";
		Database::executeUpdate($sql);
	}
	
	public static function deleteDanse($id) {
		Database::executeUpdate("DELETE FROM ".Danse::getTableName()." WHERE id = $id;");
		Database::executeUpdate("DELETE FROM ".Danse::getJoinUserTableName()." WHERE id_danse = $id");
	}
	
	public static function linkVideoDanse($id_video, $danses) {
		if (!is_array($danses)) {
			throw new Exception('MetierDanse::linkVideoDanse() : le paramètre $danses doit être un tableau');
		}
		Database::executeUpdate("DELETE FROM ".Danse::getJoinVideoTableName()." WHERE id_video = $id_video;");
		
		foreach ($danses as $id_danse) {
			Database::executeUpdate("INSERT INTO ".Danse::getJoinVideoTableName()." (id_video, id_danse) ".
				" VALUES ($id_video, $id_danse)");
		}
	}
	
	public static function removeLinkVideoDanse($id_video) {
		Database::executeUpdate("DELETE FROM ".Danse::getJoinVideoTableName()." WHERE id_video = $id_video;");
	}
	
}

?>