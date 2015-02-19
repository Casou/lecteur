<?php

header('Content-Type: text/html; charset=utf-8');

include_once "includes.php";

// Database::beginTransaction();
// Database::executeUpdate("INSERT INTO ".Passe::getTableName()." (nom, niveau, id_video) VALUES ('test', '10', 1);");
// Database::executeUpdate("INSERT INTOaze ".Passe::getTableName()." (nom, niveau, id_video) VALUES ('test2', '10', 1);");
// Database::commit();

// MetierEncodageEnCours::startEncodingVideos('test.mp4');

// $results = Database::getResultsObjects("select * from ".EncodageEnCours::getTableName(), "EncodageEnCours");
// print_r($results);

/*
$videos = MetierEncodageEnCours::getRunningEncodingVideos();
if (count($videos) > 0) {
	echo "Encodage de vidéos en cours : ";
	foreach ($videos as $video) {
		echo $video->nom_video.", ";
	}
} else {
	echo "Pas d'encodage";
}
*/

?>