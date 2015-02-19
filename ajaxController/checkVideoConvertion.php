<?php
$pathToPhpRoot = "../";

date_default_timezone_set("Europe/Paris");

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['fileToConvert'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'fileToConvert' non renseigné";
	exit;
}

Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$fileName = htmlspecialchars_decode(resetSimpleQuote($_POST['fileToConvert']));

Logger::debug("Check vidéo : $fileName");

$video = MetierEncodageEnCours::getByFileName($fileName);

// Si l'enregistrement n'existe plus
if ($video == null) {
	header('HTTP/1.1 500 Unknown file');
	echo "Le fichier '$fileName' n'est pas référencé comme étant en cours d'encodage. Veuillez raffraichir la page.";
	exit;
}

if ($video->etat == ENCODING_STATE_RUNNING) {
	$fileNameForLog = utf8_decode($_POST['fileToConvert']);
	$filePath = $pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR."$fileNameForLog.log";
	$logFile = MetierVideo::getLogAndProgress($filePath, false, $video);
	$progress = "";
	$resting = "";
	if ($logFile != null) {
		$progress = $logFile['progress'];
		$resting = $logFile['resting'];
	}
	
	$response = new AjaxResponseObject(AJAX_STATUS_RUNNING, "Encodage en cours", array('progress' => $progress, 'resting' => $resting));
	echo json_encode_utf8($response);
	exit;
} else if ($video->etat == ENCODING_STATE_WAITING) {
	$response = new AjaxResponseObject(AJAX_STATUS_RUNNING, "Encodage en attente");
	echo json_encode_utf8($response);
	exit;
} else if ($video->etat == ENCODING_STATE_ENDED_WITH_ERRORS) {
	$response = new AjaxResponseObject(AJAX_STATUS_WARNING, "Encodage terminé avec erreurs");
	echo json_encode_utf8($response);
	exit;
} else if ($video->etat == ENCODING_STATE_ENDED_OK) {
	$response = new AjaxResponseObject(AJAX_STATUS_OK, "Encodage terminé");
	echo json_encode_utf8($response);
	exit;
} else {
	$response = new AjaxResponseObject(AJAX_STATUS_KO, "Le fichier '$fileName' est dans un statut inconnu.");
	echo json_encode_utf8($response);
// 	header('HTTP/1.1 500 Unknown status');
// 	echo "Le fichier '$fileName' est dans un statut inconnu : ".$video->etat;
	exit;
}



?>