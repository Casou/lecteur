<?php

class FwkParameter {
	
	public static function getParameter($param_context, $param_id) {
		$sql = "select * from ".DoParameter::getTable();
		$sql .= " where param_context='$param_context' and param_id='$param_id'";
		$result = Database::getResultsObjects($sql, "DoParameter");
		if ($result == null || count($result) == 0) {
			throw new Exception("FwkParameter::getParameter => Le paramètre [$param_context - $param_id] n'existe pas");
		}
		return $result[0]->valeur;
	}

	public static function getAllParameters() {
		$sql = "select * from ".DoParameter::getTable();
		return Database::getResultsObjects($sql, 'DoParameter');
	}
	
	public static function modifyParameter($context, $id, $new_value) {
		$sql = "update ".DoParameter::getTable()." set "
				 ."valeur = '".escapeSimpleQuote($new_value)."' "
				 ."where param_id = '$id' and param_context = '$context'";
				 
		Database::executeUpdate($sql);
	}
	
}

?>