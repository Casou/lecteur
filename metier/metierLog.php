<?php

class MetierLog {
	
	public static function getAllConnexionForLogin($login) {
		return Database::getResultsObjects("select * from ".Connexion::getTableName().
			" where login='$login' order by date desc LIMIT 0,10", "Connexion");
	}
	
	public static function addConnexion($login) {
		$sql = "INSERT INTO ".Connexion::getTableName()." (date, login, ip) VALUES ";
		$sql .= "(now(), '$login', '".Fwk::getIp()."');";
		Database::executeUpdate($sql);
	}
	
	public static function getLastLog($user_login) {
		global $pathToPhpRoot;
		$logFile = null;
		$lastModifDate = null;
		if ($handle = opendir($pathToPhpRoot.PATH_LOG_FOLDER)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry == "." || $entry == "..") {
					continue;
				}
				$fileName = $pathToPhpRoot.PATH_LOG_FOLDER."/".utf8_encode($entry);
				
				if (Fwk::startsWith($entry, $user_login)) {
					if ($lastModifDate == null || filemtime($fileName) > $lastModifDate) {
						$logFile = $fileName;
						$lastModifDate = filemtime($fileName);
					}
				}
			}
		}
		
		return $logFile;
	}
	
	public static function deleteLog($todays_log, $userToPurge) {
		if ($todays_log) {
			foreach($userToPurge as $user_login) {
				$logFile = MetierLog::getLastLog($user_login);
				if ($logFile != null) {
					MetierLog::unlinkLogFile($logFile);
				}
			}
			Logger::info("Suppression des logs du jour pour les utilisateurs ".print_r($userToPurge, true));
		} else {
			global $pathToPhpRoot;
			if ($handle = opendir($pathToPhpRoot.PATH_LOG_FOLDER)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry == "." || $entry == "..") {
						continue;
					}
					foreach($userToPurge as $user_login) {
						if (Fwk::startsWith($entry, $user_login)) {
							$fileName = $pathToPhpRoot.PATH_LOG_FOLDER."/".utf8_encode($entry);
							MetierLog::unlinkLogFile($fileName);
						}
					}
				}
				Logger::info("Suppression de tous les logs pour les utilisateurs ".print_r($userToPurge, true));
			} else {
				throw new Exception("Erreur lors de l'ouverture du dossier $pathToPhpRoot.PATH_LOG_FOLDER");
			}
		}
	}
	
	private static function unlinkLogFile($logFile) {
		global $pathToPhpRoot;
		Logger::debug("Suppression du fichier ".realpath($logFile));
		if (!file_exists($logFile)) {
			throw new Exception("Le fichier $logFile n'existe pas - à partir de la localisation : ".getcwd());
		}
		if(realpath(Logger::getLogFilePath()) == realpath($logFile)) {
			Logger::close();
		}
		
		if (!unlink($logFile)) {
			Logger::init($pathToPhpRoot);
			throw new Exception("Impossible de supprimer le fichier $logFile à partir de la localisation : ".getcwd()
				."\n".print_r(error_get_last(), true));
		}
		Logger::init($pathToPhpRoot);
	}
	
	public static function changeDefaultLogLevel($newDefaultLogLevel) {
		FwkParameter::modifyParameter(PARAM_CONTEXT_LOG, PARAM_ID_LOG_DEFAULT_LEVEL, $newDefaultLogLevel);
	}
	
}

?>