<?php
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

Logger::init($pathToPhpRoot);

header('Content-Type: text/html; charset=utf-8');

ini_set("max_execution_time", 0); //temps infini d'exécution

$extension_command = array(
		'.ogv' => '-loglevel info -f webm -vcodec libvpx -acodec libvorbis -ab 160000 -crf 22 -y',
		'.OGV' => '-loglevel info -f webm -vcodec libvpx -acodec libvorbis -ab 160000 -crf 22 -y'
		);
$defaultCommand = "-loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y";

MetierEncodageEnCours::queueAllFiles($pathToPhpRoot.PATH_RAW_FILE);
$waitingVideos = MetierEncodageEnCours::getWaitingEncodingVideos();

foreach ($waitingVideos as $video) {
	$fileName = $video->nom_video;
	MetierEncodageEnCours::startEncodingVideos($fileName);
	$fileNameCommand = utf8_decode($fileName);
	
	if (!endsWith($fileName, ".webm")) {
		
		$extension = Fwk::getFileExtension($fileName);
		$command = "";
		if (array_key_exists($extension, $extension_command)) {
			$command = $extension_command[$extension];
		} else {
			$command = $defaultCommand;
		}
	
		$filePath = $pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$fileNameCommand;
		$ffmpegPath = $pathToPhpRoot.PATH_FFMPEG;
		$outputFilePath = $pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.formatFileName($fileName).".webm";
	
		$output = null;
		$rv = null;
		$cmdFfmpeg = "\"$ffmpegPath\" -i \"$filePath\" $command \"$outputFilePath\" 2>\"$filePath\".log";
		Logger::debug("Encodage de la vidéo '$fileName' ==> $cmdFfmpeg");
		exec($cmdFfmpeg, $output, $rv);
	
	} else {
		$filePath = $pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$fileNameCommand;
		$newFilePath = $pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.formatFileName($fileName);
	
		copy($filePath, $newFilePath);
		$rv = 0;
	}
	
	if ($rv == 0) {
		MetierEncodageEnCours::updateEncodingState($fileName, ENCODING_STATE_ENDED_OK);
	} else {
		MetierEncodageEnCours::updateEncodingState($fileName, ENCODING_STATE_ENDED_WITH_ERRORS);
	}
}

header('HTTP/1.1 200 OK');

$response = new AjaxResponseObject(AJAX_STATUS_OK, "Encodage terminé");
echo json_encode_utf8($response);
exit;

?>