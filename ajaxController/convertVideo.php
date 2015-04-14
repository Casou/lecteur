<?php
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['fileToConvert'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'fileToConvert' non renseigné";
	exit;
}

Logger::init($pathToPhpRoot);

ini_set("max_execution_time", 0); //temps infini d'exécution

$fileName = $_POST['fileToConvert'];
$command = $_POST['command'];

if (trim($command) == "") {
	$response = new AjaxResponseObject(AJAX_STATUS_KO, "La commande de conversion est vide");
	echo json_encode_utf8($response);
	exit;
}

MetierEncodageEnCours::startEncodingVideos($fileName);
$fileNameCommand = utf8_decode($fileName);

if (!endsWith($fileName, ".webm")) {
	
	$filePath = $pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$fileNameCommand;
	$ffmpegPath = $pathToPhpRoot.PATH_FFMPEG;
	$outputFilePath = $pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.formatFileName($fileName).".webm";
	
	$output = null;
	$rv = null;
	exec("\"$ffmpegPath\" -i \"$filePath\" $command \"$outputFilePath\" 2>\"$filePath\".log", $output, $rv);
	/*
	if (endsWith($fileName, ".ogv")) {
		exec("\"$ffmpegPath\" -i \"$filePath\" -loglevel info -f webm -vcodec libvpx -acodec libvorbis -ab 160000 -crf 22 -y \"$outputFilePath\" 2>\"$filePath\".log", $output, $rv);
	} else if (endsWith($fileName, ".avi") || endsWith($fileName, ".mov")) {
		exec("\"$ffmpegPath\" -i \"$filePath\" -loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y \"$outputFilePath\" 2>\"$filePath\".log");
	} else {
		exec("\"$ffmpegPath\" -i \"$filePath\" -loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y \"$outputFilePath\" 2>\"$filePath\".log");
	}
	*/
	
} else {
	$filePath = $pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$fileNameCommand;
	$newFilePath = $pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.formatFileName($fileName);
	
	copy($filePath, $newFilePath);
	$rv = 0;
}

if ($rv == 0) {
	MetierEncodageEnCours::updateEncodingState($fileName, ENCODING_STATE_ENDED_OK);
	
	$response = new AjaxResponseObject(AJAX_STATUS_OK, "Encodage terminé avec succès");
	echo json_encode_utf8($response);
	exit;
} else {
	MetierEncodageEnCours::updateEncodingState($fileName, ENCODING_STATE_ENDED_WITH_ERRORS);
	
	$response = new AjaxResponseObject(AJAX_STATUS_KO, "Encodage terminé avec erreur. Voir le fichier de log.");
	echo json_encode_utf8($response);
	exit;
}



?>