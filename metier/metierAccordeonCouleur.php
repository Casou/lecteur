<?php

class MetierAccordeonCouleur {
	
	private static $defaultColor = null;
	
	public static function getDefaultColor() {
		if (MetierAccordeonCouleur::$defaultColor == null) {
			MetierAccordeonCouleur::$defaultColor = new AccordeonCouleur();
			MetierAccordeonCouleur::$defaultColor->id = DEFAULT_EVENT_COLOR_ID;
			MetierAccordeonCouleur::$defaultColor->libelle = DEFAULT_EVENT_COLOR_LIBELLE;
			MetierAccordeonCouleur::$defaultColor->css_class = DEFAULT_EVENT_COLOR_CSS;
		}
		return MetierAccordeonCouleur::$defaultColor;
	}
	
	public static function getAllAccordeonCouleur() {
		return Database::getResultsObjects("SELECT * FROM ".AccordeonCouleur::getTableName(), "AccordeonCouleur");
	}
	
	public static function getAccordeonCouleurById($id) {
		$sql = "select * from ".AccordeonCouleur::getTableName()." where id = $id";
		$results = Database::getResultsObjects($sql, "AccordeonCouleur");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
}

?>