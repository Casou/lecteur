<?php

include_once "do/includeDo.php";

class Database {
	
// 	private static $PDO = null;
	private static $CONNEXION = null;
	
	private static function connectDB() {
		if (Database::$CONNEXION == null) {
			Database::$CONNEXION = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
			mysql_select_db(DB_NAME, Database::$CONNEXION);
			mysql_query("SET NAMES utf8;", Database::$CONNEXION);
		}
		return Database::$CONNEXION;
	}
	
	public static function disconnectDB() {
		if (Database::$CONNEXION == null) {
			throw new Exception("Database::disconnectDB() : Aucune connexion ouverte.");
		}
		mysql_close(Database::$CONNEXION);
	}
	
	public static function getMaxId($tableName) {
		$sql = "SELECT max(id) as new_id FROM $tableName";
		$results = Database::getResults($sql);
		if ($results[0]["new_id"] == null) {
			return 0;
		}
		return $results[0]["new_id"];
	}
	
	public static function getResults($sql, $log = true) {
		try {
			if ($log) {
				Logger::debug("Execute query (getResults) : $sql");
			}
			Database::connectDB();
			$results = array();
			$result = mysql_query($sql, Database::$CONNEXION);
			if ($result === false) {
				throw new Exception("La méthode mysql_query renvoie FALSE : ".mysql_error());
			}
			
			while ($row = mysql_fetch_assoc($result)) {
				$results[] = $row;
			}
			
			/*
			$stmt = Database::getPdo()->query($sql);
			while (false !== ($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
				$results[] = $row;
			}
			*/
			
			return $results;
// 		} catch (PDOException $e) {
		} catch (Exception $e) {
			print "<h1>Erreur lors de la récupération des résultats.</h1>".
				"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	public static function getResultsObjects($sql, $className) {
		try {
			Logger::debug("Execute query (getResultsObjects) : $sql");
			// Logger::debug("Execute query (getResultsObjects) : $sql \t|| ".Fwk::getCallingMethod());
			
			$dbResults = Database::getResults($sql, false);
			$results = Fwk::buildObjectsFromDatabaseResult($dbResults, new $className());
			
			/*
			$results = array();
			$stmt = Database::getPdo()->query($sql);
			while (false !== ($result = $stmt->fetchObject())) {
				$results[] = $result;
			}
			*/
				
			return $results;
// 		} catch (PDOException $e) {	
		} catch (Exception $e) {
			print "<h1>Erreur lors de la récupération des résultats.</h1>".
					"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			die();
		}
		
		
	}
	
	
	public static function beginTransaction() {
		Logger::debug("Begin transaction (annulé à cause de PDO)");
// 		Database::getPdo()->beginTransaction();
	}
	
	public static function commit() {
		Logger::debug("Commit (annulé à cause de PDO)");
// 		Database::getPdo()->commit();
	}
	
	public static function rollback() {
		Logger::debug("Rollback (annulé à cause de PDO)");
	}
// 	public static function rollback($throwPdoException = false) {
// 		try {
// 			Logger::debug("Rollback");
// 			Database::getPdo()->rollback();
// 		} catch (PDOException $e) {
// 			// L'exception est lancée si aucune transaction n'est active
// 			if ($throwPdoException) {
// 				throw $e;
// 			}
// 		}
// 	}
	
	public static function executeUpdate($sql) {
		try {
			Logger::info("Execute update : $sql");
			Database::connectDB();
			if (mysql_query($sql, Database::$CONNEXION) === false) {
				Logger::error("Erreur lors de l'exécution de la requête : $sql\n".
						print_r(mysql_error(), true));
				print_r(mysql_error());
			}
			/*
 			if (Database::getPdo()->exec($sql) === false) {

				Logger::error("Erreur lors de l'exécution de la requête : $sql\n".
					print_r(Database::getPdo()->errorInfo(), true));
				print_r(Database::getPdo()->errorInfo());
			} 
			*/
		} catch (Exception $e) {
// 		} catch (PDOException $e) {
			print "<h1>Erreur lors de l'exécution de la mise à jour.</h1>".
					"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
// 			Database::getPdo()->rollback();
			die();
		}
	}
	
	/*
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
	*/
	
}

?>