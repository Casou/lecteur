<?php

class MetierVideo {
	
	public static function getAllVideo() {
		return Database::getResultsObjects("select * from ".Video::getTableName()." ORDER BY id ASC;", "Video");
	}
	
	public static function getAllVideoEmpty() {
		return Database::getResultsObjects("select * from ".Video::getTableName()." WHERE type IS NULL ORDER BY id ASC;", "Video");
	}
	
	public static function getDureeTotale() {
		$sql = "select sum(duree) as somme from ".Video::getTableName()." v ".
			"INNER JOIN ".Video::getJoinAllowedTableName()." allw ON v.id = allw.id_video ".
			"WHERE allw.id_user=".CONNECTED_USER_ID;
		$results = Database::getResults($sql);
		return $results[0]["somme"];
	}
	
	public static function getNbVideos() {
		$sql = "select count(*) as compte from ".Video::getTableName()." v ".
			"INNER JOIN ".Video::getJoinAllowedTableName()." allw ON v.id = allw.id_video ".
			"WHERE allw.id_user=".CONNECTED_USER_ID;
		$results = Database::getResults($sql);
		return $results[0]["compte"];
	}
	
	public static function getNbVideosByEvenementAndDanse($id_evenement, $id_danse, $id_user) {
		$sql = "select count(*) as compte from ".Video::getTableName()." v ".
			"INNER JOIN ".Danse::getJoinVideoTableName()." vd on v.id = vd.id_video ".
			" inner join ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ".
			"where id_evenement = $id_evenement and id_danse = $id_danse AND allw.id_user = $id_user";
		
		$results = Database::getResults($sql);
		return $results[0]["compte"];
	}
	
	public static function getVideoById($id, $verifDroit = false) {
		if (!$verifDroit) {
			$sql = "select * from ".Video::getTableName()." where id = $id";
		} else {
			$sql = "select v.* from ".Video::getTableName()." v ".
				"inner join ".Danse::getJoinVideoTableName()." dv ON dv.id_video = v.id ".
				" where id = $id";
		}
		$results = Database::getResultsObjects($sql, "Video");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getVideoByCodePartage($code_partage, $verifDroit = false) {
		$sql = "select * from ".Video::getTableName()." where code_partage = '$code_partage'";
		$results = Database::getResultsObjects($sql, "Video");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getVideoByIdBetweenBornes($idMin, $idMax = null) {
		$query = "select * from ".Video::getTableName()." WHERE id >= $idMin ";
		if ($idMax != null) {
			$query .= "AND id <= $idMax";
		}
		return Database::getResultsObjects($query, "Video");
	}
	
	public static function getVideoByEvenement($id_evenement) {
		$sql = "select v.* from ".Video::getTableName()." v ".
			" where id_evenement = $id_evenement";
		return Database::getResultsObjects($sql, "Video");
	}
	
	public static function getVideoByDanse($id_danse) {
		$sql = "select v.* from ".Danse::getJoinVideoTableName()." dv ".
				" INNER JOIN ".Video::getTableName()." v ON v.id = dv.id_video ".
				" where id_danse = $id_danse";
		return Database::getResultsObjects($sql, "Video");
	}
	
	public static function getVideoByDanseAndEvenementWithAttributes($id_danse, $id_evenement, $id_user, $forceVisible = false) {
		// L'identifiant NO_EVENT_VIDEO_ID correspond aux vidéos sans évènement associées.
		if ($id_evenement == NO_EVENT_VIDEO_ID) {
			$sql = "select v.* from ".Danse::getJoinVideoTableName()." dv ".
					" inner join ".Video::getTableName()." v on v.id = dv.id_video ".
					" inner join ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ";
			if (!$forceVisible) {
				$sql .= " inner join ".Danse::getJoinUserTableName()." ud on dv.id_danse = ud.id_danse and ud.id_user = $id_user ";
			}
			$sql .= " where dv.id_danse = $id_danse and v.id_evenement is null ".
					"		and allw.id_user = $id_user";
		} else {
			$sql = "select v.* from ".Danse::getJoinVideoTableName()." dv ".
					" inner join ".Video::getTableName()." v on v.id = dv.id_video ".
					" inner join ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ";
			if (!$forceVisible) {
				$sql .= " inner join ".Danse::getJoinUserTableName()." ud on dv.id_danse = ud.id_danse and ud.id_user = $id_user ";
			}
			$sql .= " where dv.id_danse = $id_danse and v.id_evenement = $id_evenement ".
					"		and allw.id_user = $id_user";
		}
		$videos = Database::getResultsObjects($sql, "Video");
		$videosDTO = array();
		foreach($videos as $video) {
			$videosDTO[] = MetierVideo::getVideoAttributes($video);
		}
		return $videosDTO;
	}
	
	public static function getVideoByDanseAndNiveauWithAttributes($id_danse, $niveau) {
		$sql = "select distinct v.* from ".Danse::getJoinVideoTableName()." dv ".
				" inner join ".Video::getTableName()." v on v.id = dv.id_video ".
				" inner join ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ".
				" inner join ".Passe::getTableName()." p on dv.id_video = p.id_video ".
				" where dv.id_danse = $id_danse and p.niveau = '$niveau' and allw.id_user = ".CONNECTED_USER_ID;
		$videos = Database::getResultsObjects($sql, "Video");
		$videosDTO = array();
		foreach($videos as $video) {
			$videosDTO[] = MetierVideo::getVideoAttributes($video);
		}
		return $videosDTO;
	}
	
	public static function getVideoByDanseAndProfWithAttributes($id_danse, $id_prof) {
		$sql = "select distinct v.* from ".Danse::getJoinVideoTableName()." dv ".
				" inner join ".Video::getTableName()." v on v.id = dv.id_video ".
				" inner join ".Professeur::getJoinVideoTableName()." pv on pv.id_video = dv.id_video ".
				" inner join ".Video::getJoinAllowedTableName()." allw ON pv.id_video = allw.id_video ".
				" where dv.id_danse = $id_danse and pv.id_professeur = $id_prof and allw.id_user=".CONNECTED_USER_ID;
		
		$videos = Database::getResultsObjects($sql, "Video");
		$videosDTO = array();
		foreach($videos as $video) {
			$videosDTO[] = MetierVideo::getVideoAttributes($video);
		}
		return $videosDTO;
	}
	
	public static function getVideoWithPasses($id) {
		$dto = new VideoDTO();
		
		$video = MetierVideo::getVideoById($id);
		$dto->video = $video;
		$dto->passes = MetierPasse::getPasseByVideo($video->id);
		return $dto;
	}
	
	public static function getVideoAttributes($video) {
		$dto = new VideoDTO();
		$dto->video = $video;
		if ($video->id_evenement != null) {
			$dto->evenement = MetierEvenement::getEvenementById($video->id_evenement);
		}
		$dto->danses = MetierDanse::getDanseByVideo($video->id);
		$dto->professeurs = MetierProfesseur::getProfesseurByVideo($video->id);
		$dto->passes = MetierPasse::getPasseByVideo($video->id);
		$dto->tags = MetierTag::getTagObjectByVideo($video->id);
		$dto->isFavori = MetierVideo::isFavori($video->id);
		
		return $dto;
	}
	
	
	public static function getNextVideoId($id) {
		$sql = "SELECT id as next_id FROM ".Video::getTableName()." WHERE id > $id LIMIT 0,1";
		$results = Database::getResults($sql);
		if (count($results) == 0 || $results[0]["next_id"] == null) {
			return null;
		}
		return $results[0]["next_id"];
	}
	
	public static function getPreviousVideoId($id) {
		$sql = "SELECT id as next_id FROM ".Video::getTableName().
			" WHERE id < $id ORDER BY id DESC LIMIT 0,1";
		$results = Database::getResults($sql);
		if (count($results) == 0 || $results[0]["next_id"] == null) {
			return null;
		}
		return $results[0]["next_id"];
	}
	
	
	
	public static function getCountVideosSansEvenement($id_user, $id_danse) {
		$sql = "SELECT count(v.id) as cpt FROM ".Video::getTableName()." v ".
				" INNER JOIN ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ".
				" INNER JOIN ".Danse::getJoinVideoTableName()." vd on v.id = vd.id_video ".
				" WHERE allw.id_user = $id_user ".
				"	AND vd.id_danse = $id_danse".
				"	AND v.id_evenement is null";
		$result = Database::getResults($sql);
		return $result[0]['cpt'];
	}
	
	public static function getAllVideosSansEvenement($id_user, $id_danse) {
		$sql = "SELECT v.* FROM ".Video::getTableName()." v ".
				" INNER JOIN ".Video::getJoinAllowedTableName()." allw on v.id = allw.id_video ".
				" INNER JOIN ".Danse::getJoinVideoTableName()." vd on v.id = vd.id_video ".
				" WHERE allw.id_user = $id_user ".
				"	AND vd.id_danse = $id_danse".
				"	AND v.id_evenement is null";
		return Database::getResultsObjects($sql, "Video");
	}
	
	/*
	public static function getAllVideosWithAttributesForDanseEvenement() {
		$allDanses = MetierDanse::getAllDanse();
		
		$arrayAllVideos = array();
		foreach ($allDanses as $danse) {
			$arrayAllVideos[$danse->nom] = array();
			$videosDo = MetierVideo::getVideoByDanse($danse->id);
			
			foreach($videosDo as $do) {
				$dto = MetierVideo::getVideoAttributes($do);
				
				if($dto->evenement == null) {
					$dto->evenement = MetierEvenement::getEventStatic();
				}
				
				if (!isset($arrayAllVideos[$danse->nom][$dto->evenement->id])) {
					$arrayAllVideos[$danse->nom][$dto->evenement->id] = array(
							"evenement" => $dto->evenement, 
							"videos" => array());
				}
				
				$arrayAllVideos[$danse->nom][$dto->evenement->id]["videos"][] = $dto;
			}
		}
		
		// Tri par date (fonction "compareEvent" dans le fichier fonctions.php)
		foreach ($arrayAllVideos as $nomDanse => $arrayEvent) {
			usort($arrayEvent, "compareEvent");
			$arrayAllVideos[$nomDanse] = $arrayEvent;
			
			// TODO Ajout des vidéos dans évènements 
		}
		
		
		return $arrayAllVideos;
	}
	*/
	
	
	public static function getAllVideosWithAttributesForDanseNiveau() {
		$allDanses = MetierDanse::getDanseActivatedByUser(CONNECTED_USER_ID);
	
		$arrayAllVideos = array();
		foreach ($allDanses as $danse) {
			$niveaux = MetierPasse::getNiveauxByDanse($danse->id);
			
			$arrayAllVideos[$danse->id] = $niveaux;
			/*
			$arrayAllVideos[$danse->nom] = array();
			$videosDo = MetierVideo::getVideoByDanse($danse->id);
				
				
			foreach($videosDo as $do) {
				$dto = MetierVideo::getVideoAttributes($do);
				
				if($dto->passes != null) {
					foreach ($dto->passes as $passe) {
						if (!isset($arrayAllVideos[$danse->nom][$passe->niveau])) {
							$arrayAllVideos[$danse->nom][$passe->niveau] = array();
						}
						
						if (!isset($arrayAllVideos[$danse->nom][$passe->niveau][$dto->video->id])) {
							$arrayAllVideos[$danse->nom][$passe->niveau][$dto->video->id] = $dto;
							
						}
					}
				}
			}
			*/
		}
		
		/*
		foreach($arrayAllVideos as $key => $arrayNiveau) {
			ksort($arrayAllVideos[$key]);
		}
		*/
		
		// On supprime les danses vides (sans vidéos)
		foreach ($arrayAllVideos as $danse_id => $dto) {
			if (count($arrayAllVideos[$danse_id]) == 0) {
				unset($arrayAllVideos[$danse_id]);
			}
		}
		
		return $arrayAllVideos;
	}
	
	
	
	
	public static function getAllVideosWithAttributesForDanseProfesseur() {
		$allDanses = MetierDanse::getDanseActivatedByUser(CONNECTED_USER_ID);
	
		$arrayAllVideos = array();
		foreach ($allDanses as $danse) {
			$arrayAllVideos[$danse->id] = MetierProfesseur::getProfesseursByDanse($danse->id);
		}
		/*
		foreach ($allDanses as $danse) {
			$arrayAllVideos[$danse->nom] = array();
			$videosDo = MetierVideo::getVideoByDanse($danse->id);
	
	
			foreach($videosDo as $do) {
				$dto = MetierVideo::getVideoAttributes($do);
	
				if($dto->professeurs != null) {
					foreach ($dto->professeurs as $professeur) {
						if (!isset($arrayAllVideos[$danse->nom][$professeur->nom])) {
							$arrayAllVideos[$danse->nom][$professeur->nom] = array();
						}
	
						if (!isset($arrayAllVideos[$danse->nom][$professeur->nom][$dto->video->id])) {
							$arrayAllVideos[$danse->nom][$professeur->nom][$dto->video->id] = $dto;
								
						}
					}
				}
			}
		}
	
		foreach($arrayAllVideos as $key => $arrayNiveau) {
			ksort($arrayAllVideos[$key]);
		}
		*/
		
		foreach ($arrayAllVideos as $danse_id => $dto) {
			if (count($arrayAllVideos[$danse_id]) == 0) {
				unset($arrayAllVideos[$danse_id]);
			}
		}
	
		return $arrayAllVideos;
	}
	
	public static function getAllVideosWithAttributesFavori() {
		$allDanses = MetierDanse::getAllDanse();
	
		$arrayAllVideos = array();
		foreach ($allDanses as $danse) {
			$arrayAllVideos[$danse->id] = array();
			$videosDo = MetierVideo::getVideoFavoriByDanse($danse->id);
				
			foreach($videosDo as $do) {
				$dto = MetierVideo::getVideoAttributes($do);
	
// 				if($dto->evenement == null) {
// 					$dto->evenement = MetierEvenement::getEventStatic();
// 				}
	
				$arrayAllVideos[$danse->id][] = $dto;
			}
			
			if (count($arrayAllVideos[$danse->id]) == 0) {
				unset($arrayAllVideos[$danse->id]);
			}
		}
	
		return $arrayAllVideos;
	}
	
	
	
	
	
	
	
	
	
	
	public static function completeVideo($fileName, $duration = null) {
		$hasTransaction = Database::beginTransaction();
		
		$fileNameWebm = formatFileName($fileName);
		if (!endsWith($fileName, ".webm")) {
			$fileNameWebm .= ".webm";
		}
		
		$newId = Database::getMaxId(Video::getTableName()) + 1;
		
		$fileNameCommand = utf8_decode($fileName);
		
		// On mets la vidéo brute dans la corbeille
		$filePath = "..".DIRECTORY_SEPARATOR.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$fileNameCommand;
		$newFilePath = "..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$fileNameCommand;
		rename($filePath, $newFilePath);

		// On renomme le webm avec l'id de la vidéo
		$filePathWebm = "..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$fileNameWebm;
		$newFilePathWebm = "..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$newId.'_'.$fileNameWebm;
		rename($filePathWebm, $newFilePathWebm);
		
		// On supprimpe le fichier de log
		if (file_exists("$filePath.log")) {
			unlink("$filePath.log");
		}
		
		MetierEncodageEnCours::deleteEncodedVideo($fileName);
		MetierVideo::insertVideo($newId.'_'.$fileNameWebm, $newId);
		
		// On crée un fichier de sous-titre vide
		touch("..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.escapeSpaces($newId.'_'.$fileNameWebm).'.vtt');
		
		if ($duration != null) {
			$duree = $duration;
		} else {
			$duree = MetierVideo::getVideoDuration($newId.'_'.$fileNameWebm);
			MetierVideo::generateThumbnail($newId.'_'.$fileNameWebm);
		}
		
		$sql = "UPDATE lct_video SET duree=$duree WHERE id = $newId;";
		Database::executeUpdate($sql);
		
		if ($hasTransaction) Database::commit();
	}
	
	private static function getVideoDuration($fileName) {
		$path = "..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$fileName;
		
		Logger::debug("Recherche de la durée de la vidéo $path");
		
		ob_start();
		passthru("..".DIRECTORY_SEPARATOR.PATH_FFMPEG." -i \"$path\" 2>&1");
		$duration = ob_get_contents();
		ob_end_clean();
		
		preg_match('/Duration: (.*?),/', $duration, $matches);
		$duration = $matches[1];
		
		$dureeEnSecondes = Fwk::parseDureeEnSecondes($duration);
		Logger::debug("Durée trouvée : $dureeEnSecondes secondes.");
		
		return $dureeEnSecondes;
	}
	
	
	private static function generateThumbnail($fileName) {
		$path = "..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$fileName;
		$path_thumbnail = "..".DIRECTORY_SEPARATOR.PATH_THUMBNAIL.DIRECTORY_SEPARATOR.$fileName.'.jpg';
		
		Logger::debug("Génération de l'aperçu");
		
		// ffmpeg.exe -ss 00:15  -i %1 -vcodec mjpeg -vframes 3 -an -f rawvideo -s 240x160 "%output_path%\%~2.jpg" -y
		passthru("..".DIRECTORY_SEPARATOR.PATH_FFMPEG." -ss 00:15 -i \"$path\" -vcodec mjpeg -vframes 3 -an -f rawvideo -s 240x160 \"$path_thumbnail\"");
		
		Logger::debug("Aperéu généré : ".$path_thumbnail);
	}
	
	
	public static function insertVideo($fileName, $newId = null) {
		if ($newId == null) {
			$newId = Database::getMaxId(Video::getTableName()) + 1;
		}
		$sql = "INSERT INTO ".Video::getTableName()."(id, nom_video, nom_affiche, code_partage) VALUES  ($newId, '".escapeString($fileName)."',  '".escapeString($fileName)."', "
			." MD5(CONCAT($newId, '".escapeString($fileName)."', NOW())));";
		Database::executeUpdate($sql);
	}
	
	public static function saveVideoProperties($formulaire) {
		// Toutes les entrées du formulaire sont parsées en variables
		Logger::debug("formulaire : ".print_r(($_POST['formulaire']), true));
		// Only Free : Free rajoute des "\" automatiquement
		parse_str(stripslashes($_POST['formulaire']));
		
		if (!isset($id) || !isset($nom_video) || !isset($nom_affiche)
				|| !isset($type) || !isset($danse)) {
			throw new Exception("Tous les paramètres n'ont pas été remplis.");
		}
		
		if (!isset($id_evenement)) {
			$id_evenement = 'NULL';
		}
		
		$hasTransaction = Database::beginTransaction();
		
		Logger::debug(">> nom_video : $nom_video => ".escapeString(stripslashes($nom_video)));
		
		$sql = "UPDATE ".Video::getTableName()." set ".
			"nom_video = '".escapeString(stripslashes($nom_video))."'".
			", nom_affiche = '".escapeString(stripslashes($nom_affiche))."'".
			", type = '$type'".
			", id_evenement = $id_evenement".
			" WHERE id=$id;";
		Database::executeUpdate($sql);
		
		if (!isset($danse)) {
			$danse = array();
		}
		MetierDanse::linkVideoDanse($id, $danse);
		
		if (!isset($tag)) {
			$tag = array();
		}
		MetierTag::linkVideoTag($id, $tag);
		
		if (!isset($playlist)) {
			$playlist = array();
		}
		MetierPlaylist::linkVideoPlaylist($id, $playlist);
		
		if (!isset($passe) || !isset($niveau) || !isset($timer_debut) || !isset($timer_fin)) {
			$passe = array();
			$niveau = array();
			$timer_debut = array();
			$timer_fin = array();
		}
		MetierPasse::linkVideoPasse($id, $passe, $niveau, $timer_debut, $timer_fin, $nom_video);
		
		if (!isset($professeur)) {
			$professeur = array();
		}
		MetierProfesseur::linkVideoProfesseur($id, $professeur);
		
		if (!isset($profils)) {
			$profils = array();
		}
		MetierProfil::saveProfilsAllowedForVideo(array($id), $profils, true);
		
		if (!isset($users)) {
			$users = array();
		}
		MetierUser::saveUserAllowedForVideo(array($id), $users, true);
		
		if ($hasTransaction) Database::commit();
	}
	
	
	
	
	
	public static function research($formulaire, $limit = null) {
		parse_str($formulaire);
		
		$sql = 	"SELECT DISTINCT v.* FROM ".Video::getTableName()." v ".
				" inner join ".Video::getJoinAllowedTableName()." allw ON v.id = allw.id_video ";
		$join = "";
		$where = "";
		$criteres = array();
		$criteres["danse"] = array();
		$criteres["evenement"] = array();
		$criteres["niveau"] = array();
		$criteres["type"] = array();
		$criteres["professeur"] = array();
		$criteres["nom_affiche"] = array();
		$criteres["passe"] = array();
		$criteres["tag"] = array();
		$criteres["coeff"] = array();
		$nbCriteres = 0;
		$joinPasse = false;
		
		$operator = "AND";
		if ($operatorCriteres == "all") {
			$operator = "AND";
		} else if ($operatorCriteres == "one") {
			$operator = "OR";
		}
		
		// Construction de la clause WHERE
		// Filtre sur les danses
		if (isset($danse) && is_array($danse)) {
			$nbCriteres++;
			$join .= " LEFT OUTER JOIN ".Danse::getJoinVideoTableName()." vd on v.id = vd.id_video ";
			$where .= " $operator vd.id_danse IN (";
			$isFirst = true;
			foreach ($danse as $danseId) {
				if (!$isFirst) {
					$where .= ", ";
				}
				$where .= $danseId;
				$isFirst = false;
				
				$criteres["danse"][] = $danseId;
			}
			$where .= ")";
			
			$criteres["coeff"]["danse"] = floatval($coeff_danse);
		}
		
		// Filtre sur les types (cours, stages, ...)
		if (isset($type) && is_array($type)) {
			$nbCriteres++;
			$where .= " $operator v.type IN (";
			$isFirst = true;
			foreach ($type as $typeId) {
				if (!$isFirst) {
					$where .= ", ";
				}
				$where .= "'$typeId'";
				$isFirst = false;
				
				$criteres["type"][] = $typeId;
			}
			$where .= ")";
			$criteres["coeff"]["type"] = floatval($coeff_type);
		}
		
		// Filtre sur les évènements
		if (isset($evenement) && is_array($evenement)) {
			$nbCriteres++;
			
			$where .= " $operator v.id_evenement IN (";
			$isFirst = true;
			foreach ($evenement as $evenementId) {
				if (!$isFirst) {
					$where .= ", ";
				}
				$where .= "$evenementId";
				$isFirst = false;
				
				$criteres["evenement"][] = $evenementId;
			}
			$where .= ")";
			$criteres["coeff"]["evenement"] = floatval($coeff_evenement);
		}
		
		// Filtre sur les niveaux
		if (isset($niveau) && is_array($niveau)) {
			$nbCriteres++;
			$join .= " LEFT OUTER JOIN ".Passe::getTableName()." p on v.id = p.id_video ";
			$joinPasse = true;
			$where .= " $operator p.niveau IN (";
			$isFirst = true;
			foreach ($niveau as $niveauId) {
				if (!$isFirst) {
					$where .= ", ";
				}
				$where .= "'$niveauId'";
				$isFirst = false;
		
				$criteres["niveau"][] = $niveauId;
			}
			$where .= ")";
			$criteres["coeff"]["niveau"] = floatval($coeff_niveau);
		}
		
		// Filtre sur les profs
		if (isset($professeur) && is_array($professeur)) {
			$nbCriteres++;
			$join .=  " LEFT OUTER JOIN ".Professeur::getJoinVideoTableName()." pv on v.id = pv.id_video ";
			$where .= " $operator pv.id_professeur IN (";
			$isFirst = true;
			foreach ($professeur as $professeurId) {
				if (!$isFirst) {
					$where .= ", ";
				}
				$where .= "'$professeurId'";
				$isFirst = false;
		
				$criteres["professeur"][] = $professeurId;
			}
			$where .= ")";
			$criteres["coeff"]["professeur"] = floatval($coeff_professeur);
		}
		
		// Filtre sur les noms des vidéos
		if (isset($nom_affiche) && trim($nom_affiche) != "") {
			$nbCriteres++;
			$allWords = explode_research_field($nom_affiche);
			foreach ($allWords as $word) {
				$where .= " $operator UPPER(v.nom_affiche) LIKE UPPER('%$word%') ";
		
				$criteres["nom_affiche"][] = $word;
			}
			$criteres["coeff"]["nom_affiche"] = floatval($coeff_nom_affiche);
		}
		
		// Filtre sur les noms des passes
		if (isset($passe) && trim($passe) != "") {
			$nbCriteres++;
			
			if (!$joinPasse) {
				$join .= " LEFT OUTER JOIN ".Passe::getTableName()." p on v.id = p.id_video ";
				$joinPasse = true;
			}
			$allWords = explode_research_field($passe);
			foreach ($allWords as $word) {
				$where .= " $operator UPPER(p.nom) LIKE UPPER('%$word%') ";
		
				$criteres["passe"][] = $word;
			}
			$criteres["coeff"]["passe"] = floatval($coeff_passe);
		}
		
		// Filtre sur les tags
		if (isset($tag) && is_array($tag)) {
			$nbCriteres++;
			$join .= " LEFT OUTER JOIN ".Tag::getJoinVideoTableName()." tv on v.id = tv.id_video ";
			
			$where .= " $operator tv.id_tag IN (";
			$isFirst = true;
			foreach ($tag as $tagId) {
				if (!$isFirst) {
					$where .= ", ";
				}
				$where .= $tagId;
				$isFirst = false;
		
				$criteres["tag"][] = $tagId;
			}
			$where .= ")";
			$criteres["coeff"]["tag"] = floatval($coeff_tag);
		}
		
		// Filtre sur les vidéos qui ont des passes
		if (isset($only_no_passes)) {
			$join .= " LEFT OUTER JOIN ".Passe::getTableName()." no_passes ON v.id = no_passes.id_video ";
			$where .= " AND no_passes.id_video is null ";
		}
		
		$where = substr($where, strlen($operator) + 2); // On retire le premier opérateur
		$where = " WHERE v.type IS NOT NULL AND allw.id_user=".CONNECTED_USER_ID." AND ($where)";
		
		$sql .= $join.$where;
		
		if ($limit != null) {
			$sql .= " LIMIT 0,$limit";
		}
		
		$videos = Database::getResultsObjects($sql, "Video");
		
		$videosDTO = array();
		foreach($videos as $video) {
			$dto = MetierVideo::getVideoAttributes($video);
			if ($operatorCriteres != "all") {
				$dto->pertinence = MetierVideo::calculPertinence($dto, $criteres, $nbCriteres);
			} else {
				$dto->pertinence = 1;
			}
			$videosDTO[] = $dto;
		}
		
		return $videosDTO;
	}
	
	
	private static function calculPertinence($dto, $criteres, $nbCriteres) {
		$nbCriteresOK = 0;
		if (count($criteres["danse"]) > 0) {
			foreach ($dto->danses as $danse) {
				if (in_array($danse->id, $criteres["danse"])) {
					$nbCriteresOK += $criteres["coeff"]["danse"];
					break;
				}
			}
		}
		
		if (count($criteres["type"]) > 0) {
			if (in_array($dto->video->type, $criteres["type"])) {
				$nbCriteresOK += $criteres["coeff"]["type"];
			}
		}
		
		if (count($criteres["evenement"]) > 0) {
			if (in_array($dto->video->id_evenement, $criteres["evenement"])) {
				$nbCriteresOK += $criteres["coeff"]["evenement"];
			}
		}
		
		if (count($criteres["niveau"]) > 0) {
			foreach ($dto->passes as $passe) {
				if (in_array($passe->niveau, $criteres["niveau"])) {
					$nbCriteresOK += $criteres["coeff"]["niveau"];
					break;
				}
			}
		}
		
		if (count($criteres["professeur"]) > 0) {
			$valueCritere = 1 / count($criteres["professeur"]);
			foreach ($dto->professeurs as $professeur) {
				if (in_array($professeur->id, $criteres["professeur"])) {
					$nbCriteresOK += $valueCritere * $criteres["coeff"]["professeur"];
				}
			}
		}
		
		if (count($criteres["nom_affiche"]) > 0) {
			$valueCritere = 1 / count($criteres["nom_affiche"]);
			$nom_video = mb_strtoupper(stripAccents($dto->video->nom_affiche));
			
			foreach($criteres["nom_affiche"] as $index => $mot) {
				if (strrpos($nom_video, mb_strtoupper(stripAccents($mot))) !== false) {
					$nbCriteresOK += $valueCritere * $criteres["coeff"]["nom_affiche"];
					unset($criteres["nom_affiche"][$index]);
					break;
				}
			}
		}
			
// 			foreach ($dto->passes as $passe) {
// // 				if (array_search($passe->nom, $criteres["nom_affiche"]) !== false) {
// // 					$nbCriteresOK++;
// // 					break;
// // 				}
// 				foreach($criteres["nom_affiche"] as $nom) {
// 					if (strrpos(strtoupper($passe->nom), strtoupper($nom))) {
// 						$nbCriteresOK++;
// 						break;
// 					}
// 				}
// 			}
		
		if (count($criteres["passe"]) > 0) {
			$valueCritere = 1 / count($criteres["passe"]);
// 			$passeOK = false;
			foreach ($dto->passes as $passe) {
// 				if ($passeOK) {
// 					break;
// 				}
				foreach($criteres["passe"] as $index => $mot) {
					if (strrpos(mb_strtoupper(stripAccents($passe->nom)), mb_strtoupper(stripAccents($mot))) !== false) {
						$nbCriteresOK += $valueCritere * $criteres["coeff"]["passe"];
// 						$passeOK = true;
						unset($criteres["passe"][$index]);
						break;
					}
				}
			}
		}
		
		if (count($criteres["tag"]) > 0) {
			$valueCritere = 1 / count($criteres["tag"]);
			foreach ($dto->tags as $tag) {
				if (in_array($tag->id, $criteres["tag"])) {
					$nbCriteresOK += $valueCritere * $criteres["coeff"]["tag"];
				}
			}
		}
		
		return $nbCriteresOK / $nbCriteres;
	}
	
	
	
	public static function majStatsThumbnail() {
		global $pathToPhpRoot;
		
		$all_videos = MetierVideo::getAllVideo();
		$errors = array();
		$nb_errors = 0;
		foreach ($all_videos as $video) {
			if (!file_exists($pathToPhpRoot.PATH_THUMBNAIL."/$video->nom_video.jpg")) {
				if (count($errors) <= NB_THUMBNAIL_ERRORS) {
					$errors[] = $video;
				}
				$nb_errors++;
			}
		}
		
		$date_text = "[".date('d/m/Y')."]";
		$text = "$date_text <b>".count($all_videos)."</b> vidéos : ";
		if ($nb_errors == 0) {
			$text .= "Tous les aperçus sont <b>OK</b>.";
		} else {
			$title = "";
			if (count($errors) >= NB_THUMBNAIL_ERRORS) {
				$title .= NB_THUMBNAIL_ERRORS." premières erreurs affichées\n";
			}
			foreach($errors as $error) {
				$title .= "$error->nom_video \n";
			}
			$text .= "<b>$nb_errors</b> aperçus introuvables. <span style='cursor : help' title='$title'>[?]</span>";
		}
		
		/*
		$text = mb_convert_encoding($text, 'UTF-8', 'OLD-ENCODING');
		file_put_contents($pathToPhpRoot.PATH_THUMBNAIL_FILE, $text);
		*/
		Fwk::writeInFile($pathToPhpRoot.PATH_THUMBNAIL_FILE, utf8_encode($text), 'w');
		
		return $text;
	}
	
	
	
	
	public static function deleteVideo($id) {
		$hasTransaction = Database::beginTransaction();
		
		Logger::info("Suppression de la vidéo $id");
		
		$video = MetierVideo::getVideoById($id);
		
		MetierDanse::removeLinkVideoDanse($id);
		MetierTag::removeLinkVideoTag($id);
		MetierPasse::removeLinkVideoPasse($id);
		MetierProfesseur::removeLinkVideoProfesseur($id);
		
		Database::executeUpdate("DELETE FROM ".Video::getTableName()." WHERE id = $id;");
		
		$videoFilePath = "..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video;
		if (file_exists($videoFilePath)) {
			Logger::debug("Suppression du fichier vidéo : $videoFilePath");
			if (!unlink($videoFilePath)) {
				throw new Exception("Le fichier \"$videoFilePath\" n'a pas pu être supprimé.");
			}
		}
		
		$vttFilePath = "..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.escapeSpaces($video->nom_video).".vtt";
		if (file_exists($vttFilePath)) {
			Logger::debug("Suppression du fichier VTT : $vttFilePath");
			if (!unlink($vttFilePath)) {
				throw new Exception("Le fichier de sous-titre \"$vttFilePath\" n'a pas pu être supprimé.");
			}
		}
		
		Logger::info("Suppression de la vidéo $id ==> OK");
		if ($hasTransaction) Database::commit();
	}
	
	public static function deleteRawVideo($nom) {
		if (!unlink("..".DIRECTORY_SEPARATOR.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$nom)) {
			throw new Exception("Le fichier \"..".DIRECTORY_SEPARATOR.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$nom.
					"\" n'a pas pu être supprimé.");
		}
	}
	
	public static function deleteBinVideo($nom) {
		if ($nom == "all") {
			if ($handle = opendir("..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != '.' && $entry != '..') {
// 						$fileName = utf8_encode($entry);
						$fileName = $entry;
						if (!unlink("..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$fileName)) {
							throw new Exception("Le fichier \"..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$fileName.
									"\" n'a pas pu être supprimé.");
						}
					}
				}
				closedir($handle);
			} else {
				throw new Exception("Erreur lors de l'ouverture du dossier \"..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN."\".");
			}
		} else {
			if (!unlink("..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$nom)) {
				throw new Exception("Le fichier \"..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$nom.
						"\" n'a pas pu être supprimé.");
			}
		}
	}
	
	public static function moveBinVideo($nom) {
		if (!rename("..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$nom,
				"..".DIRECTORY_SEPARATOR.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$nom)) {
			throw new Exception("Le fichier \"..".DIRECTORY_SEPARATOR.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$nom.
					"\" n'a pas pu être déplacé.");
		}
	}
	
	
	public static function getLogAndProgress($filePath, $returnKOIfFileDoesntExists = true, $videoEncodage = null) {
		if (!file_exists($filePath)) {
			if ($returnKOIfFileDoesntExists) {
				$response = new AjaxResponseObject(AJAX_STATUS_KO, "Le fichier $filePath n'existe pas.");
				echo Fwk::json_encode_utf8($response);
				exit;
			}
			return null;
		}
		
		$fileContent = "";
		$duration = "??";
		$time = "??";
		/*Ouverture du fichier en lecture seule*/
		$handle = fopen($filePath, 'r');
		/*Si on a réussi à ouvrir le fichier*/
		if ($handle) {
			/*Tant que l'on est pas à la fin du fichier*/
			while (!feof($handle)) {
				/*On lit la ligne courante*/
				$buffer = fgets($handle);
				$fileContent .= $buffer."\n";
				
				if ($duration == "??") {
					$durationIndex = Fwk::lastIndexOf($buffer, "Duration");
					if ($durationIndex !== false && $durationIndex >= 0) {
						// Exemple : Duration: 00:01:51.61, start: 0.000000, bitrate: 9015 kb/s
						$bufferDuration = substr($buffer, $durationIndex + strlen("Duration: "));
						$duration = substr($bufferDuration, 0, 11);
					}
				}
		
				if (Fwk::startsWith($buffer, "frame= ")) {
					// Exemple : frame= 3345 fps=6.7 q=0.0 size=    6489kB time=00:01:51.50 bitrate= 476.8kbits/s
					$timeIndex = Fwk::lastIndexOf($buffer, "time=");
					$bufferDuration = substr($buffer, $timeIndex + strlen("time="));
					$time = substr($bufferDuration, 0, 11);
				}
			}
			/*On ferme le fichier*/
			fclose($handle);
		}
		
		$progress = "??";
		try {
			if ($duration != "??" && $time != "??") {
				$durationExploded = explode(":", $duration);
				$durationInSecond = $durationExploded[0] * 3600;
				$durationInSecond += $durationExploded[1] * 60;
				$durationSeconds = explode(".", $durationExploded[2]);
				$durationInSecond += $durationSeconds[0];
		
				$timeExploded = explode(":", $time);
				$timeInSecond = $timeExploded[0] * 3600;
				$timeInSecond += $timeExploded[1] * 60;
				$timeSeconds = explode(".", $timeExploded[2]);
				$timeInSecond += $timeSeconds[0];
		
				$progress = formatNumber(($timeInSecond / $durationInSecond * 100), 2);
			}
		} catch (Exception $e) {
			// On ne fait rien
		}
		
		$diff = "?? sec.";
		if ($videoEncodage != null && $progress != "??" && $progress != 0) {
			$pourcentageRestant = 100 - $progress;
			
			$diffDates = Fwk::dateDiff(date("Y-m-d H:i:s"), $videoEncodage->debut_encodage);
			
			$nbSecondesPourUnPourcent = $diffDates / $progress;
			
			$diffSeconds = round($nbSecondesPourUnPourcent * $pourcentageRestant);
			
			$diffMinutes = 0;
			$diffHeures = 0;
			if ($diffSeconds > 60) {
				$diffMinutes = round($diffSeconds / 60, 0);
				$diffSeconds = $diffSeconds % 60;
				
				if ($diffMinutes > 60) {
					$diffHeures = round($diffMinutes / 60, 0);
					$diffMinutes = $diffMinutes % 60;
				}
			}
			
			$diff = "";
			if ($diffHeures > 0) {
				$diff .= Fwk::lpad($diffHeures, 2, "0")."h ";
			}
			if ($diffMinutes > 0) {
				$diff .= Fwk::lpad($diffMinutes, 2, "0")."m ";
			}
			$diff .= Fwk::lpad($diffSeconds, 2, "0")."s";
			
		}
		
		return array(
				'fileContent' => $fileContent,
				'duration' => $duration,
				'time' => $time,
				'progress' => htmlspecialchars($progress)." %",
				'resting' => $diff);
	}
	
	public static function changeNomAfficheVideo($idVideo, $nouveauNom) {
		$sql = "UPDATE ".Video::getTableName()." SET nom_affiche = '".escapeString($nouveauNom).
			"' WHERE id = $idVideo;";
		Database::executeUpdate($sql);
	}
	
	
	public static function isFavori($id_video) {
		$sql = "select f.* from ".Video::getJoinFavoriTableName()." f ".
				" where id_user = ".CONNECTED_USER_ID." and f.id_video = $id_video";
		return count(Database::getResultsObjects($sql, "Video")) > 0;
	}
	
	public static function changeFavori($idVideo, $action) {
		if ($action == "addFavori") {
			$sql = "INSERT INTO ".Video::getJoinFavoriTableName()."(id_user, id_video) ".
				"VALUES (".CONNECTED_USER_ID.", $idVideo)";
		} else if ($action == "removeFavori") {
			$sql = "DELETE FROM ".Video::getJoinFavoriTableName().
					" WHERE id_user = ".CONNECTED_USER_ID." and id_video = $idVideo";
		} else {
			throw new Exception("Action inconnue pour la méthode changeFavori : $action");
		}
		Database::executeUpdate($sql);
	}
	
	
	public static function getVideoFavoriByDanse($danseId) {
		$sql = "SELECT v.* FROM ".Danse::getJoinVideoTableName()." dv ".
				" INNER JOIN ".Video::getTableName()." v ON v.id = dv.id_video ".
				" INNER JOIN ".Video::getJoinFavoriTableName()." f ON v.id = f.id_video ".
				" WHERE id_danse = $danseId AND id_user = ".CONNECTED_USER_ID;
		return Database::getResultsObjects($sql, "Video");
	}
	
	
}

?>