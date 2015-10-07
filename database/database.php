<?php

include_once "do/includeDo.php";

class Database {
	
 	private static $PDO = null;
// 	private static $CONNEXION = null;
	
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
	
	/*
	private static function connectDB() {
		if (Database::$CONNEXION == null) {
			Database::$CONNEXION = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if (mysqli_connect_errno()) {
				printf("Échec de la connexion : %s\n", mysqli_connect_error());
				exit();
			}
			mysqli_query(Database::$CONNEXION, "SET NAMES utf8;");
			mysqli_set_charset(Database::$CONNEXION, "utf8");
		}
		return Database::$CONNEXION;
	}
	
	public static function disconnectDB() {
		if (Database::$CONNEXION == null) {
			throw new Exception("Database::disconnectDB() : Aucune connexion ouverte.");
		}
		mysqli_close(Database::$CONNEXION);
	}
	*/
	
	public static function disconnectDB() {
		Database::$PDO = null;
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
			/*
			Database::connectDB();
			$result = mysqli_query(Database::$CONNEXION, $sql);
			if ($result === false) {
				throw new Exception("La méthode mysqli_query renvoie FALSE : ".mysqli_error(Database::$CONNEXION));
			}
			
			while ($row = mysqli_fetch_assoc($result)) {
				$results[] = $row;
			}
			*/
			
			$results = array();
			$stmt = Database::getPdo()->query($sql);
			while (false !== ($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
				$results[] = $row;
			}
			
			return $results;
 		} catch (PDOException $e) {
//		} catch (Exception $e) {
			print "<h1>Erreur lors de la récupération des résultats.</h1>".
				"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	public static function getResultsObjects($sql, $className) {
		try {
			Logger::debug("Execute query (getResultsObjects) : $sql");
			// Logger::debug("Execute query (getResultsObjects) : $sql \t|| ".Fwk::getCallingMethod());
			
			/*
			$dbResults = Database::getResults($sql, false);
			$results = Fwk::buildObjectsFromDatabaseResult($dbResults, new $className());
			*/
			
			
			$results = array();
			$stmt = Database::getPdo()->query($sql);
			while (false !== ($result = $stmt->fetchObject())) {
				$results[] = $result;
			}
				
			return $results;
 		} catch (PDOException $e) {	
// 		} catch (Exception $e) {
			print "<h1>Erreur lors de la récupération des résultats.</h1>".
					"Requête : $sql<br/><br/>" . $e->getMessage() . "<br/>";
			die();
		}
		
		
	}
	
	
	public static function beginTransaction() {
		Logger::debug("Begin transaction (annulé à cause de PDO)");
		try {
 			Database::getPdo()->beginTransaction();
 			return true;
		} catch (PDOException $e) {
			Logger::warn("beginTransaction : ".$e->getMessage());
			return false;
		}
	}
	
	public static function commit() {
		Logger::debug("Commit (annulé à cause de PDO)");
 		Database::getPdo()->commit();
	}
	
	public static function rollback($throwPdoException = false) {
		Logger::debug("Rollback (annulé à cause de PDO)");
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
			Logger::warn("Execute update : $sql");
			/*
			Database::connectDB();
			if (mysqli_query(Database::$CONNEXION, $sql) === false) {
				Logger::error("Erreur lors de l'exécution de la requête : $sql\n".
						print_r(mysqli_error(Database::$CONNEXION), true));
				throw new Exception(print_r(mysqli_error(Database::$CONNEXION), true));
			}
			*/
 			if (Database::getPdo()->exec($sql) === false) {
				Logger::error("Erreur lors de l'exécution de la requête : $sql\n".
					print_r(Database::getPdo()->errorInfo(), true));
				print_r(Database::getPdo()->errorInfo());
			} 
//		} catch (Exception $e) {
 		} catch (PDOException $e) {
			print "<h1>Erreur lors de l'exécution de la mise à jour.</h1>\n".
					"Requête : $sql<br/><br/>\n\n" . $e->getMessage() . "<br/>";
 			Database::getPdo()->rollback();
			die();
		}
	}
	
}

?>