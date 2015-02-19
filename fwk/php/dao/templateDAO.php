<?php

class TemplateDAO {
	
	// Execute la requête et renvoie un résultat unique (le premier)
	public static function getSingleResult($query) {
		$result = TemplateDAO::executeQuery($query);
		if (!$result) {
			die(mysql_error());
		}
		return mysql_fetch_array($result);
	}
	
	// Execute la requête et renvoie un tableau de résultats
	public static function getMultipleResult($query) {
		$result = TemplateDAO::executeQuery($query);
		if (!$result) {
			die(mysql_error());
		}
		
		$results = array();
		while($row = mysql_fetch_array($result)) {
			$results[sizeof($results)] = $row;
		}
		
		return $results;
	}

	public static function executeQuery($query) {
		Logger::debug("Query : $query");
		
		$result = mysql_query($query, MetierDB::getConnexion());
		if (!$result) {
			Logger::error(mysql_error());
			throw new Exception(mysql_error());
		}
		return $result;
	}
	
	
	public static function findAll($doToSearch) {
		$sql = "select * from ".$doToSearch->getTable();
		return TemplateDAO::getMultipleResult($sql);
	}
	
	
	public static function findById($doToSearch, $id) {
		$sql = "select * from ".$doToSearch->getTable()." where id = $id";
		return TemplateDAO::getSingleResult($sql);
	}
	
	public static function deleteFromId($doToSearch, $id) {
		$sql = "delete from ".$doToSearch->getTable()." where id = $id";
		return TemplateDAO::executeQuery($sql);
	}
	
	public static function getLastInsertId() {
		return mysql_insert_id();
	}
	
	
	public static function merge($doToMerge, $showNull = true) {
		// print_r($doToMerge);
	
		$query = "";
		$id = null;
		$fieldNames = array();
		$fieldNamesQuery = "";
		$fieldValues = array();
		$fieldValuesQuery = "";
		
		$reflectionClass = new ReflectionClass($doToMerge);
		$props = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
		
		foreach ($doToMerge->getTableFields() as $propName) {
			$prop = $reflectionClass->getProperty($propName);
			if ($prop->getName() == "id") {
				$id = $prop->getValue($doToMerge);
			} else {
				$fieldNames[count($fieldNames)] = $prop->getName();
				$fieldValues[count($fieldValues)] = $prop->getValue($doToMerge);
			}
		}
				
		// Création
		if ($id == null) {
			for ($i = 0; $i < count($fieldNames); $i++) {
				if (!Fwk::isEmpty($fieldValues[$i]) || $showNull) {
					if ($i > 0) {
						$fieldNamesQuery .= ", ";
						$fieldValuesQuery .= ", ";
					}
					
					$fieldNamesQuery .= $fieldNames[$i];
					if (Fwk::isEmpty($fieldValues[$i])) {
						if (Fwk::startsWith($fieldNames[$i], "is")) {
							$fieldValuesQuery .= "0";
						} else {
							$fieldValuesQuery .= "NULL";
						}
					} else if (Fwk::startsWith($fieldNames[$i], "date")) {
						$fieldValuesQuery .= "STR_TO_DATE('".$fieldValues[$i]."', '%d/%m/%Y')";
					} else {
						$fieldValuesQuery .= "'".addslashes($fieldValues[$i])."'";
					}
				}
			}
			
			$query = "INSERT INTO ".$doToMerge->getTable()." (".$fieldNamesQuery.")";
			$query .= " VALUES (".$fieldValuesQuery.")";
			
		// Modification
		} else {
			$query = "UPDATE ".$doToMerge->getTable()." SET ";
			for ($i = 0; $i < count($fieldNames); $i++) {
				if (strpos("[".$fieldNames[$i], "[obj_") !== false) {
					continue;
				}
				
				if ($i > 0) {
					$query .= ", ";
				}
				$query .= $fieldNames[$i]." = ";
				if (Fwk::isEmpty($fieldValues[$i])) {
					$query .= "NULL";
				} else if (Fwk::startsWith($prop->getName(), "date")) {
					$query .= "STR_TO_DATE('".$fieldValues[$i]."', '%d/%m/%Y')";
				} else {
					$query .= "'".addslashes($fieldValues[$i])."'";
				}
			}
			$query .= " WHERE id = ".$id;
		}
	
		TemplateDAO::executeQuery($query);
		
		if ($id == null) {
			$id = mysql_insert_id();
			$doToMerge->id = $id;
		}
		
		return $doToMerge;
	}
	
}

?>