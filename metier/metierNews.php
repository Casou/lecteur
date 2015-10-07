<?php

class MetierNews {
	
	public static function getAllNews() {
		return Database::getResultsObjects("select * from ".News::getTableName(), "News");
	}
	
	public static function getNewsById($id) {
		$sql = "select * from ".News::getTableName()." where id = $id";
		$tag_array = Database::getResultsObjects($sql, "News");
		return $tag_array[0];
	}
	
	public static function getAvailableNews() {
		$sql = "SELECT * FROM ".News::getTableName()." WHERE now() BETWEEN date_debut AND date_fin order by date_debut asc";
		return Database::getResultsObjects($sql, "News");
	}
	

	
	public static function saveNews($formulaire) {
		parse_str($formulaire);
		parse_str($formulaire, $array);
		
		if (!isset($id)) {
			throw new Exception('La variable $id n\'est pas settée');
		}
		
		Logger::debug("Array : ".print_r($array, true));
		if ($id == null || $id == '') {
			$sql = "INSERT INTO ".News::getTableName()." (id_user_creation, date_creation, date_debut, date_fin, texte) VALUES (".
					$_SESSION["userLogged"].", now(), ".
					"'".Fwk::reformateDate($date_debut, 'd/m/Y', 'Y-m-d')."', ".
					"'".Fwk::reformateDate($date_fin, 'd/m/Y', 'Y-m-d')."', ".
					"'".Fwk::escapeSimpleQuote($texte)."')";
		} else {
			$sql = "UPDATE ".News::getTableName()." set date_debut='".Fwk::reformateDate($date_debut, 'd/m/Y', 'Y-m-d')."', ".
					"date_fin = '".Fwk::reformateDate($date_fin, 'd/m/Y', 'Y-m-d')."', ".
					"texte = '".Fwk::escapeSimpleQuote($texte)."' ".
					"WHERE id = $id";
		}
		
		Database::executeUpdate($sql);
		
	}
	
	public static function deleteNews($id) {
		$sql = "DELETE FROM ".News::getTableName()." WHERE id = $id";
		
		Database::executeUpdate($sql);
	}
	
}

?>