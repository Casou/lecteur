<?php

class FwkParameter {
	
	public static function getParameter($param_context, $param_id) {
		$sql = "select * from ".DoParameter::getTable();
		$sql .= " where param_context='$param_context' and param_id='$param_id'";
		$result = TemplateDao::getSingleResult($sql);
		if ($result == null) {
			throw new Exception("FwkParameter::getParameter => Le paramètre [$param_context - $param_id] n'existe pas");
		}
		return $result['valeur'];
	}

	public static function getAllParameters() {
		$sql = "select * from ".DoParameter::getTable();
		return TemplateDao::getMultipleResult($sql, null, 'DoParameter');
	}


	public static function enregistrerModificationsParameters() {
		$formulaire = Fwk::unserializeJQuery(Fwk::getRequestParameter(Fwk::REQUEST_METHOD_POST, "formulaire"));
		$ids = array();
		if (isset($formulaire['id'])) {
			if (is_array($formulaire['id'])) {
				foreach($formulaire['id'] as $id) {
					$ids[] = $id;
				}
			} else {
				$ids[] = $formulaire['id'];
			}
		}
		
		$sql = "update ".DoParameter::getTable()." set ";
		$sql .= "valeur = :valeur, ";
		$sql .= "description = :description ";
		$sql .= "where id = :id;";
		
		// S'il y a des champs modifiés
		foreach($ids as $id) {
			
			$valeur = $formulaire['valeur_'.$id];
			$description = $formulaire['description_'.$id];
			
			// Vérifications de surface
			if (trim($valeur) == "") {
				$param_context = $formulaire['param_context_'.$id];
				$param_id = $formulaire['param_id_'.$id];
				throw new Exception("[$param_context][$param_id]\n=> La valeur ne peut pas être vide.");
			}
			
			$param = array("valeur" => $valeur, "description" => $description, "id" => $id);
			
			$res = TemplateDao::executeUpdate($sql, $param);
			if ($res === false) {
				throw new Exception("[Id $id] La requête a échouée : $sql");
			}
		}
		
		return AjaxResultBean::build(AjaxResultBean::STATUS_OK, "Paramètres mis à jour");
	}
	
}

?>