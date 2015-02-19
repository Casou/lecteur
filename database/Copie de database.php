<?php

include_once "do/includeDo.php";

class Database {
	
	private static $PDO = null;
	
	public static function getMaxId($tableName) {
		$sql = "SELECT max(id) as new_id FROM $tableName";
		$results = Database::getResults($sql);
		if ($results[0]["new_id"] == null) {
			return 0;
		}
		return $results[0]["new_id"];
	}
	
	public static function getResults($sql) {
		try {
			Logger::debug("Execute query (getResults) : $sql");
			$results = array();
			$stmt = Database::getPdo()->query($sql);
			while (false !== ($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
				$results[] = $row;
			}
			
			return $results;
		} catch (PDOException $e) {
			print "<h1>Erreur lors de la récupération des résultats.</h1>".
				"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	public static function getResultsObjects($sql, $className) {
		try {
			// Logger::debug("Execute query (getResultsObjects) : $sql");
			Logger::debug("Execute query (getResultsObjects) : $sql \t|| ".Fwk::getCallingMethod());
			$results = array();
			$stmt = Database::getPdo()->query($sql);
			while (false !== ($result = $stmt->fetchObject())) {
				$results[] = $result;
			}
				
			return $results;
		} catch (PDOException $e) {
			print "<h1>Erreur lors de la récupération des résultats.</h1>".
					"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			die();
		}
		
		
	}
	
	
	public static function beginTransaction() {
		Logger::debug("Begin transaction");
		Database::getPdo()->beginTransaction();
	}
	
	public static function commit() {
		Logger::debug("Commit");
		Database::getPdo()->commit();
	}
	
	public static function rollback($throwPdoException = false) {
		try {
			Logger::debug("Rollback");
			Database::getPdo()->rollback();
		} catch (PDOException $e) {
			// L'exception est lancée si aucune transaction n'est active
			if ($throwPdoException) {
				throw $e;
			}
		}
	}
	
	public static function executeUpdate($sql) {
		try {
			Logger::debug("Execute update : $sql");
			// echo $sql;
// 			Database::getPdo()->beginTransaction();
			if (Database::getPdo()->exec($sql) === false) {
				Logger::error("Erreur lors de l'exécution de la requête : $sql\n".
					print_r(Database::getPdo()->errorInfo(), true));
				print_r(Database::getPdo()->errorInfo());
			} 
// 			Database::getPdo()->commit();
		} catch (PDOException $e) {
			print "<h1>Erreur lors de l'exécution de la mise à jour.</h1>".
					"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			Database::getPdo()->rollback();
			die();
		}
	}
	
	private static function getPdo() {
		if (Database::$PDO == null) {
			Database::$PDO = new PDO(
				'mysql:host='.DB_HOST.';dbname='.DB_NAME,
				DB_USER,
				DB_PASSWORD, 
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") 
			);
			Database::$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return Database::$PDO;
	}
	
}

?>