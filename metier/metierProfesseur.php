<?php

class MetierProfesseur {
	
	public static function getAllProfesseur() {
		return Database::getResultsObjects(
				"select * from ".Professeur::getTableName()." order by nom asc", "Professeur");
	}
	
	public static function getAllAllowedProfesseur() {
		return Database::getResultsObjects(
				"select distinct p.* from ".Professeur::getTableName()." p ".
				" inner join ".Professeur::getJoinVideoTableName()." pv on pv.id_professeur = p.id ".
				" inner join ".Video::getJoinAllowedTableName()." allw_vid on pv.id_video = allw_vid.id_video ".
				" WHERE id_user=".$_SESSION['userId']. 
				" order by nom asc", "Professeur");
	}
	
	public static function getProfesseurById($id) {
		$sql = "select * from ".Professeur::getTableName()." where id = $id";
		$results = Database::getResultsObjects($sql, "Professeur");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getProfesseurByNom($nom) {
		$sql = "select * from ".Professeur::getTableName()." where nom = '".escapeString($nom)."'";
		$results = Database::getResultsObjects($sql, "Professeur");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	
	public static function getProfesseurByVideo($id_video) {
		$sql = "select p.* ".
				"from ".Professeur::getJoinVideoTableName()." pv ".
				"inner join ".Professeur::getTableName()." p on p.id = pv.id_professeur ".
				"where id_video = $id_video";
		$results = Database::getResultsObjects($sql, "Professeur");
		return $results;
	}
	
	
	public static function getProfesseursByDanse($id_danse) {
		$sql = "select p.id as id_prof, p.nom as nom_prof, dv.id_danse as id_danse, count(distinct dv.id_video) as cpt ".
				" from ".Danse::getJoinVideoTableName()." dv ".
				" inner join ".Professeur::getJoinVideoTableName()." pv on pv.id_video = dv.id_video ".
				" inner join ".Professeur::getTableName()." p on pv.id_professeur = p.id ".
				" inner join ".Video::getJoinAllowedTableName()." allw ON pv.id_video = allw.id_video ".
				" where dv.id_danse = $id_danse and allw.id_user=".$_SESSION['userId'].
				" group by p.id, p.nom, dv.id_danse ".
				" ORDER BY p.nom ASC;";
		$results = Database::getResults($sql);
		return $results;
	}
	
	
	
	
	
	
	public static function insertProfesseur($nom) {
		if (trim($nom) == "") {
			throw new Exception("Le nom du professeur ne peut pas être vide.");
		}
		
		$professeur = MetierProfesseur::getProfesseurByNom($nom);
		if ($professeur != null) {
			throw new Exception("Le professeur $nom existe déjà.");	
		}
		
		$sql = "INSERT INTO ".Professeur::getTableName()."(nom) VALUES  ('".escapeString($nom)."');";
		Database::executeUpdate($sql);
		
		return Database::getMaxId(Professeur::getTableName());
	}
	
	public static function updateNomProfesseur($id, $nom) {
		if (trim($nom) == "") {
			throw new Exception("Le nom du professeur ne peut pas être vide.");
		}
	
		$professeur = MetierProfesseur::getProfesseurById($id);
		if ($professeur == null) {
			throw new Exception("Le professeur numéro $id n'existe pas en base.");
		}
	
		$sql = "UPDATE ".Professeur::getTableName()." SET nom='".escapeString($nom)."' WHERE id=$id;";
		Database::executeUpdate($sql);
	}
	
	public static function deleteProfesseur($id) {
		Database::executeUpdate("DELETE FROM ".Professeur::getTableName()." WHERE id = $id;");
	}
	
	public static function linkVideoProfesseur($id_video, $professeurs) {
		if (!is_array($professeurs)) {
			throw new Exception('MetierProfesseur::linkVideoProfesseur() : le paramètre $professeurs doit être un tableau');
		}
		Database::executeUpdate("DELETE FROM ".Professeur::getJoinVideoTableName()." WHERE id_video = $id_video;");
	
		foreach ($professeurs as $id_professeur) {
			Database::executeUpdate("INSERT INTO ".Professeur::getJoinVideoTableName()." (id_video, id_professeur) ".
					" VALUES ($id_video, $id_professeur)");
		}
	}
	
	public static function removeLinkVideoProfesseur($id_video) {
		Database::executeUpdate("DELETE FROM ".Professeur::getJoinVideoTableName()." WHERE id_video = $id_video;");
	}
	
}

?>