<?php

class MetierEvenement {
	
	private static $eventStatic = null;
	
	public static function getEventStatic() {
		if (MetierEvenement::$eventStatic == null) {
			MetierEvenement::$eventStatic = new Evenement();
			MetierEvenement::$eventStatic->id = NO_EVENT_VIDEO_ID;
			MetierEvenement::$eventStatic->nom = "<i>[Sans évènement]</i>";
			// MetierEvenement::$eventStatic->date = Fwk::parseDate("12/31/2999");
			MetierEvenement::$eventStatic->ville = "";
		}
		return MetierEvenement::$eventStatic;
	}
	
	public static function getAllEvenement($formatDate = false) {
		$allDo = Database::getResultsObjects("select * from ".Evenement::getTableName()." order by date asc", "Evenement");
		if (!$formatDate) {
			return $allDo;
		}
		$allDoReturn = array();
		foreach($allDo as $do) {
			$do->date = formatDateToDisplay($do->date);
			$allDoReturn[] = $do;
		}
		return $allDoReturn;
	}
	
	public static function getAllAllowedEvenement($formatDate = false) {
		$allDo = Database::getResultsObjects("select distinct evt.* from ".Evenement::getTableName()." evt ".
			" inner join ".Video::getTableName()." v on v.id_evenement = evt.id ".
			" inner join ".Video::getJoinAllowedTableName()." allw_vid on v.id = allw_vid.id_video ".
			" WHERE id_user=".CONNECTED_USER_ID. 
			" order by evt.date asc", "Evenement");
		if (!$formatDate) {
			return $allDo;
		}
		$allDoReturn = array();
		foreach($allDo as $do) {
			$do->date = formatDateToDisplay($do->date);
			$allDoReturn[] = $do;
		}
		return $allDoReturn;
	}
	
	public static function getAllEvenementForDanse($id_danse, $formatDate = false) {
		$allDo = Database::getResultsObjects(
				" select distinct e.* from ".Evenement::getTableName()." e ".
				" inner join ".Video::getTableName()." v on v.id_evenement = e.id ".
				" inner join ".Danse::getJoinVideoTableName()." dv on v.id = dv.id_video ".
				" where dv.id_danse = $id_danse ".
				" order by date asc", "Evenement");
	
	
		if (!$formatDate) {
			return $allDo;
		}
		$allDoReturn = array();
		foreach($allDo as $do) {
			$do->date = formatDateToDisplay($do->date);
			$allDoReturn[] = $do;
		}
		return $allDoReturn;
	}
	
	public static function getAllEvenementForAllowedVideos($id_user, $formatDate = false) {
		$allDo = Database::getResultsObjects(
				" select distinct e.* from ".Evenement::getTableName()." e ".
				" inner join ".Video::getTableName()." v on v.id_evenement = e.id ".
				" inner join ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ".
				" where allw.id_user = $id_user ".
				" order by date asc", "Evenement");
		
		
		if (!$formatDate) {
			return $allDo;
		}
		$allDoReturn = array();
		foreach($allDo as $do) {
			$do->date = formatDateToDisplay($do->date);
			$allDoReturn[] = $do;
		}
		return $allDoReturn;
	}
	
	public static function getEvenementById($id) {
		$sql = "select * from ".Evenement::getTableName()." where id = $id";
		$results = Database::getResultsObjects($sql, "Evenement");
		if (count($results) == 0) {
			return null;
		}
		$do = $results[0];
		$do->date = formatDateToDisplay($do->date);
		
		return $do;
	}
	
	public static function getEvenementByVideo($id_video, $formatDate = false) {
		$sql = "select e.* ".
				"from ".Evenement::getTableName()." e ".
				"inner join ".Video::getTableName()." v on e.id = v.id_evenement ".
				"where v.id = $id_video";
		$results = Database::getResultsObjects($sql, "Evenement");
		if (count($results) == 0) {
			return null;
		}
		$evenement = $results[0];
		if (!$formatDate) {
			$evenement->date = formatDateToDisplay($evenement->date);
		}
		return $evenement;
	}
	
	
	public static function getAllEvenementWithVideoCount($id_user, $force_all_danses = false) {
		$evenements = MetierEvenement::getAllEvenementForAllowedVideos($id_user);
		$dansesEvenements = array();
		
		foreach ($evenements as $evenement) {
			if ($force_all_danses) {
				$danses = MetierDanse::getDanseByEvenement($evenement->id, $id_user);
			} else {
				$danses = MetierDanse::getDanseActivatedByEvenement($evenement->id, $id_user);
			}
			foreach ($danses as $danse) {
				if (!isset($dansesEvenements[$danse->id])) {
					$dansesEvenements[$danse->id] = array();
				}
				
				$nbVideos = MetierVideo::getNbVideosByEvenementAndDanse($evenement->id, $danse->id, $id_user);
				
				if ($nbVideos > 0) {
					$dto = new EvenementDTO();
					$dto->evenement = $evenement;
					$dto->danse = $danse;
					$dto->couleur = MetierAccordeonCouleur::getAccordeonCouleurById($evenement->couleur);
					$dto->nbVideos = $nbVideos;
					$dansesEvenements[$danse->id][] = $dto;
				}
			}
		}
		
		$danses = MetierDanse::getAllDanse();
		// On liste (si nécessaire) les vidéos sans évènements
		foreach ($danses as $danse) {
			$nbVideosSansEvenements = MetierVideo::getCountVideosSansEvenement($id_user, $danse->id);
			if ($nbVideosSansEvenements > 0) {
				$dto = new EvenementDTO();
				$dto->evenement = MetierEvenement::getEventStatic();
				$dto->danse = $danse;
				$dto->couleur = MetierAccordeonCouleur::getDefaultColor();
				$dto->nbVideos = $nbVideosSansEvenements;
				$dansesEvenements[$danse->id][] = $dto;
			}
		}
		
		// On supprime les danses vides (sans vidéos)
		foreach ($dansesEvenements as $danse_id => $dto) {
			if (count($dansesEvenements[$danse_id]) == 0) {
				unset($dansesEvenements[$danse_id]);
			}
		}
		
		return $dansesEvenements;
	}
	
	
	public static function getAllEvenementWithVideoCountByDanse($id_danse, $id_user) {
		$evenements = MetierEvenement::getAllEvenementForDanse($id_danse);
		$evenementsArray = array();
	
		foreach ($evenements as $evenement) {
			$dto = new EvenementDTO();
			$dto->evenement = $evenement;
			$dto->nbVideos = MetierVideo::getNbVideosByEvenementAndDanse($evenement->id, $id_danse, $id_user);
			$evenementsArray[] = $dto;
		}
	
		return $evenementsArray;
	}
	
	/*
	public static function getEvenementByVideo($id_video, $formatDate = false) {
		$sql = "select e.* ".
			"from ".Evenement::getJoinVideoTableName()." ed ".
			"inner join ".Evenement::getTableName()." e on e.id = ed.id_evenement ".
			"where id_video = $id_video ".
			"order by date asc;";
		$results = Database::getResultsObjects($sql, "Evenement");
		if (!$formatDate) {
			return $results;
		}
		$resultsReturn = array();
		foreach($results as $do) {
			$do->date = formatDateToDisplay($do->date);
			$resultsReturn[] = $do;
		}
		return $resultsReturn;
	}
	*/
	
	
	
	
	
	
	public static function insertEvenement($nom, $date, $ville, $couleur) {
		if (trim($nom) == "") {
			throw new Exception("Le nom de l'évènement ne peut pas être vide.");
		}
		
		$sql = "INSERT INTO ".Evenement::getTableName()."(nom, date, ville, couleur) ".
			"VALUES ('".escapeString($nom)."', DATE('".formatDateToMysql($date)."'), '".escapeString($ville)."', $couleur);";
		Database::executeUpdate($sql);
		
		$do = new Evenement();
		$do->id = Database::getMaxId(Evenement::getTableName());
		$do->nom = $nom;
		$do->date = $date;
		$do->ville = $ville;
		$do->couleur = $couleur;
		
		return $do;
	}
	
	public static function updateEvenement($id, $nom, $date, $ville, $couleur) {
		if (trim($nom) == "") {
			throw new Exception("Le nom de l'évènement ne peut pas être vide.");
		}
	
		$evenement = MetierEvenement::getEvenementById($id);
		if ($evenement == null) {
			throw new Exception("L'évènement numéro $id n'existe pas en base.");
		}
		
		$sql = "UPDATE ".Evenement::getTableName()." SET nom='".escapeString($nom)."', ".
			"date=DATE('".formatDateToMysql($date)."'), ville='".escapeString($ville)."', couleur = $couleur WHERE id=$id;";
		Database::executeUpdate($sql);
	}
	
	public static function deleteEvenement($id) {
		Database::executeUpdate("DELETE FROM ".Evenement::getTableName()." WHERE id = $id;");
	}
	
	/*
	public static function linkVideoEvenement($id_video, $evenements) {
		if (!is_array($evenements)) {
			throw new Exception('MetierEvenement::linkVideoEvenement() : le paramètre $evenements doit être un tableau');
		}
		Database::executeUpdate("DELETE FROM ".Evenement::getJoinVideoTableName()." WHERE id_video = $id_video;");
	
		foreach ($evenements as $id_evenement) {
			Database::executeUpdate("INSERT INTO ".Evenement::getJoinVideoTableName()." (id_video, id_evenement) ".
					" VALUES ($id_video, $id_evenement)");
		}
	}
	*/
}

?>