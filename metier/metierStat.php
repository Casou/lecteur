<?php

class MetierStat {
	
	public static function getStatsVideo() {
		$dto = new StatVideoDTO();
		$dto->dureeTotale = Fwk::formatDureeEnSecondes(MetierVideo::getDureeTotale());
		$dto->nbVideos = MetierVideo::getNbVideos();
		$dto->nbPasses = MetierPasse::getNbPasse();
		
		return $dto;
	}
	
	public static function getNbVideosByDanseAndType() {
		$sql = "SELECT d.id, d.nom, v.type, count(*) as nb
				FROM ".Video::getTableName()." v
				INNER JOIN ".Video::getJoinAllowedTableName()." allw ON v.id = allw.id_video
				INNER JOIN ".Danse::getJoinVideoTableName()." vd ON v.id = vd.id_video
				INNER JOIN ".Danse::getTableName()." d ON d.id = vd.id_danse
				WHERE allw.id_user=".$_SESSION['userId']."
				GROUP BY d.id, d.nom, v.type
				ORDER BY d.id;";
		
		$results = Database::getResults($sql);
		
		$arrayDTO = array();
		foreach($results as $result) {
			if (!isset($arrayDTO[$result['nom']])) {
				$dto = new StatDTO();
				$dto->idDanse = $result['id'];
				$dto->nomDanse = $result['nom'];
			} else {
				$dto = $arrayDTO[$result['nom']];
			}
			
			$dto->arrayTypeNombre[$result['type']] = $result['nb'];
			$arrayDTO[$result['nom']] = $dto;
		}
		
		return $arrayDTO;
	} 
	
}

?>