<?php

class MetierDroit {
	
	public static function getAllDroit() {
		return Database::getResultsObjects("select * from ".Droit::getTableName()." order by ordre asc", "Droit");
	}
	
	public static function getDroitsByUser($idUser) {
		$sql = "select d.* from ".Droit::getJoinUserTableName()." du ".
			" inner join ".Droit::getTableName()." d on du.id_droit = d.id ".
			" where du.id_user = $idUser";
		$results = Database::getResultsObjects($sql, "Droit");
		return $results;
	}
	
	public static function saveDroit($formulaire) {
		parse_str($formulaire);
		
		Database::beginTransaction();
		
		$users = MetierUser::getAllUser();
		$droits = MetierDroit::getAllDroit();
		
		$sql = "delete from ".Droit::getJoinUserTableName().";";
		Database::executeUpdate($sql);
		
		foreach ($users as $user) {
			foreach ($droits as $droit) {
				$checkBoxName = $user->id."_".$droit->nom;
				if (isset($$checkBoxName)) {
					$sql = "INSERT INTO ".Droit::getJoinUserTableName()." (id_droit, id_user) VALUES ";
					$sql .= "($droit->id, $user->id);";
					Database::executeUpdate($sql);
				}
			}	
		}
		
		Database::commit();
	}
	
}

?>