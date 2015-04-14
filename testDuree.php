<?php

header('Content-Type: text/html; charset=utf-8');

include_once "includes.php";
Logger::init();

ini_set("max_execution_time", 0); //temps infini d'exécution

/*
$video = MetierVideo::getVideoById(305);
// $video = $videos[0];

$path = PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video;
echo "$path <br/>";

ob_start();
passthru(PATH_FFMPEG." -i \"$path\" 2>&1");
$duration = ob_get_contents();
ob_end_clean();

preg_match('/Duration: (.*?),/', $duration, $matches);
$duration = $matches[1];

$dureeEnSecondes = Fwk::parseDureeEnSecondes($duration);
echo "$dureeEnSecondes <br/>";

$dureeFormatee = Fwk::formatDureeEnSecondes($dureeEnSecondes);
echo "$dureeFormatee <br/>";
*/

/*
$duration_array = explode(':', $duration);
$duration = $duration_array[0] * 3600 + $duration_array[1] * 60 + round($duration_array[2], 0);
*/
// print_r($duration);



/*
$videos = MetierVideo::getAllVideo();

Database::beginTransaction();

foreach($videos as $video) {
	$duree = MetierVideo::getVideoDuration($video->nom_video);
	$sql = "UPDATE lct_video SET duree=$duree WHERE id = $video->id";
	Database::executeUpdate($sql);
}

Database::commit();
*/

?>