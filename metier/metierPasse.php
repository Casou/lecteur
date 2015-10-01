<?php

class MetierPasse {
	
	public static function getAllPasse() {
		return Database::getResultsObjects("select * from ".Passe::getTableName(), "Passe");
	}
	
	public static function getNbPasse() {
		$sql = "select count(distinct nom) as compte from ".Passe::getTableName()." p ".
			" INNER JOIN ".Video::getJoinAllowedTableName()." allw ON p.id_video = allw.id_video ".
			" WHERE allw.id_user=".CONNECTED_USER_ID;
		$results = Database::getResults($sql);
		return $results[0]["compte"];
	}
	
	public static function getPasseByVideo($id_video) {
		// $sql = "select * from ".Passe::getTableName()." where id_video = $id_video ORDER BY niveau ASC;";
		$sql = "select * from ".Passe::getTableName()." where id_video = $id_video ORDER BY timer_debut ASC;";
		$results = Database::getResultsObjects($sql, "Passe");
		return $results;
	}
	
	public static function getNiveauxByDanse($id_danse) {
		$sql = "select niveau as nom_niveau, dv.id_danse as id_danse, count(distinct dv.id_video) as cpt ".
			" from ".Danse::getJoinVideoTableName()." dv ".
			" inner join ".Passe::getTableName()." p on dv.id_video = p.id_video ".
			" inner join ".Video::getJoinAllowedTableName()." allw on p.id_video = allw.id_video ".
			" where dv.id_danse = $id_danse and allw.id_user = ".CONNECTED_USER_ID.
			" group by niveau, dv.id_danse ".
			" ORDER BY niveau ASC;";
		$results = Database::getResults($sql);
		return $results;
	}
	
	
	public static function linkVideoPasse($id_video, $passes, $niveaux, $timers_debut, $timers_fin, $nom_video) {
		if (!is_array($passes)) {
			throw new Exception('MetierPasse::linkVideoPasse() : le paramètre $passes doit être un tableau');
		}
		if (!is_array($niveaux)) {
			throw new Exception('MetierPasse::linkVideoPasse() : le paramètre $niveaux doit être un tableau');
		}
		if (count($passes) != count($niveaux)) {
			throw new Exception('MetierPasse::linkVideoPasse() : il n\'y a pas le même nombre de passes et de niveaux');
		}
		
		Database::executeUpdate("DELETE FROM ".Passe::getTableName()." WHERE id_video = $id_video;");
		
		
		// Ecriture du fichier de sous-titre
		Logger::debug("Ecriture du fichier de sous-titre");
// 		$srtFileName = "$nom_video.srt";
		$vttFileName = "$nom_video.vtt";
		$vttFile = fopen("..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.escapeSpaces($vttFileName), 'w');
		Fwk::writeInOpenedFile($vttFile, "WEBVTT");
		Fwk::writeInOpenedFile($vttFile, "");
		
// 		$numeroSrt = 1;
		for ($i = 0; $i < count($passes); $i++) {
			$nom = stripslashes($passes[$i]);
			$niveau = $niveaux[$i];
			$timer_debut = $timers_debut[$i];
			$timer_fin = $timers_fin[$i];
			
			if ($timer_debut == "00:00:00" && $timer_fin == "00:00:00") {
				$timer_debut = "";
				$timer_fin = "";
			}
			
			$timer = false;
			if ($timer_debut != "" && $timer_fin != "") {
				$timer = true;
			}
			
			$sql = "INSERT INTO ".Passe::getTableName()." (nom, niveau, id_video";
			if ($timer) {  
				$sql .= ", timer_debut, timer_fin";
			}
			
			$sql .= ") VALUES ('".escapeString($nom)."', '".escapeString($niveau)."', $id_video";
			if ($timer) {
				$sql .= ", '$timer_debut', '$timer_fin'";
			}
			$sql .= ");";
			
			Database::executeUpdate($sql);
			
			if ($timer) {
// 				Logger::debug("Passe $numeroSrt; $timer_debut,000 --> $timer_fin,000 : ".utf8_encode($nom));
				
// 				Fwk::writeInOpenedFile($vttFile, "$numeroSrt");
				Fwk::writeInOpenedFile($vttFile, "$timer_debut.000 --> $timer_fin.000");
				Fwk::writeInOpenedFile($vttFile, utf8_encode($nom));
				Fwk::writeInOpenedFile($vttFile, "");
				
// 				$numeroSrt++;
			}
		}
		fclose($vttFile);
	}
	
	public static function removeLinkVideoPasse($id_video) {
		Database::executeUpdate("DELETE FROM ".Passe::getTableName()." WHERE id_video = $id_video;");
	}
	
	public static function hasPasseTimed($id_video) {
		$sql = "select * from ".Passe::getTableName().
			" where id_video = $id_video AND timer_debut IS NOT NULL;";
		$results = Database::getResults($sql);
		return count($results) > 0;
	}
	
}

?>