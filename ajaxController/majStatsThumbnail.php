<?php
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

Logger::init($pathToPhpRoot);

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

try {
	$fichier = MetierVideo::majStatsThumbnail();
	$response = new AjaxResponseObject(AJAX_STATUS_OK, $fichier);
} catch (Exception $e) {
	$response = new AjaxResponseObject(AJAX_STATUS_KO, $e->getMessage());
}

echo json_encode_utf8($response);
exit;

?>