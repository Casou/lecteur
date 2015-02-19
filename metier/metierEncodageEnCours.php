<?php

class MetierEncodageEnCours {
	
	public static function getByFileName($fileName) {
		$sql = "select * from ".EncodageEnCours::getTableName()." where nom_video = '".escapeString($fileName)."'";
		$results = Database::getResultsObjects($sql, "EncodageEnCours");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getRunningEncodingVideos() {
		return Database::getResultsObjects("select * from ".EncodageEnCours::getTableName(), "EncodageEnCours");
	}
	
	public static function getWaitingEncodingVideos() {
		return Database::getResultsObjects("select * from ".EncodageEnCours::getTableName().
				" where etat = ".ENCODING_STATE_WAITING, "EncodageEnCours");
	}
	
	public static function startEncodingVideos($fileName) {
		$file = MetierEncodageEnCours::getByFileName($fileName);
		
		if ($file == null) {
			$sql = "INSERT INTO ".EncodageEnCours::getTableName()."(nom_video, debut_encodage, etat) VALUES  (";
			$sql .= "'".escapeString($fileName)."', now(), ".ENCODING_STATE_RUNNING.");";
			Database::executeUpdate($sql);
		} else {
			MetierEncodageEnCours::updateEncodingState($fileName, ENCODING_STATE_RUNNING);
		}
	}
	
	public static function queueAllFiles($folderPath) {
		if ($handle = opendir($folderPath)) {
			while (false !== ($entry = readdir($handle))) {
				// $fileName = htmlspecialchars_decode(utf8_encode(htmlspecialchars($entry)));
				$fileName = utf8_encode($entry);
		
				if ($fileName == "." || $fileName == ".." || endsWith($fileName, ".log")) {
					continue;
				}
		
				$videoEncodee = MetierEncodageEnCours::getByFileName($fileName);
				if ($videoEncodee == null) {
					$sql = "INSERT INTO ".EncodageEnCours::getTableName()."(nom_video, debut_encodage, etat) VALUES  (";
					$sql .= "'".escapeString($fileName)."', now(), ".ENCODING_STATE_WAITING.");";
					Database::executeUpdate($sql);
				}
			}
		} else {
			throw new Exception("Le dossier " + $folderPath + " n'a pas pu être ouvert.");
		}
	}
	
	
	public static function updateEncodingState($fileName, $state) {
		$sql = "UPDATE ".EncodageEnCours::getTableName();
		$sql .= " SET etat = $state, debut_encodage = now() ";
		$sql .= " WHERE nom_video = '".escapeString($fileName)."';";
		Database::executeUpdate($sql);
	}
	
	public static function deleteEncodedVideo($fileName) {
		$sql = "DELETE FROM ".EncodageEnCours::getTableName();
		$sql .= " WHERE nom_video = '".escapeString($fileName)."';";
		Database::executeUpdate($sql);
	}
	
}

?>