<?php

class MetierDroitCategorie {
	
	public static function getAllDroitByCategories() {
		$categories = Database::getResultsObjects("select * from ".DroitCategorie::getTableName(), "DroitCategorie");
		$droits_categories = array();
		foreach ($categories as $categorie) {
			$dto = new DroitCategorieDTO();
			$dto->droit_categorie = $categorie;
			$dto->droits = Database::getResultsObjects(
				"select * from ".Droit::getTableName()." where id_droit_categorie = $categorie->id order by ordre asc", 
				"Droit");
			
			$droits_categories[] = $dto;
		}
		
		return $droits_categories;
	}
	
}

?>