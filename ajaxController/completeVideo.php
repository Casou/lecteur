<?php
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

Logger::init($pathToPhpRoot);

if (!isset($_POST['fileToComplete'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'fileToConvert' non renseigné";
	exit;
}

$duree = null;
if (isset($_POST['duree'])) {
	$duree = $_POST['duree'];
}

$fileName = htmlspecialchars_decode(resetSimpleQuote($_POST['fileToComplete']));
$ok = true;
try {
	if (MetierVideo::completeVideo($fileName, $duree) === false) {
		$ok = false;
	}
} catch (Exception $e) {
	$ok = false;	
}


if ($ok) {
	$response = new AjaxResponseObject(AJAX_STATUS_OK, "Vidéo transférée");
	echo json_encode_utf8($response);
	exit;
} else {
	$response = new AjaxResponseObject(AJAX_STATUS_KO, "Une erreur s'est produite.");
	echo json_encode_utf8($response);
	exit;
}

?>