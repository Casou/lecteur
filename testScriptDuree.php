<?php
header('Content-Type: text/html; charset=utf-8');

include_once "includes.php";
Logger::init();

ini_set("max_execution_time", 0); //temps infini d'exécution

echo "<pre>";
$videos = Database::getResultsObjects("select * from ".Video::getTableName()." WHERE id >= 436", "Video");

foreach($videos as $video) {
	echo "UPDATE ".Video::getTableName()." SET duree = $video->duree WHERE SUBSTRING(nom_video,4) = ".
		"SUBSTRING('".escapeString($video->nom_video)."', 4);\n";
}

echo "</pre>";
?>