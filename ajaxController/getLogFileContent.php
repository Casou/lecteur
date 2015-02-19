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

Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$fileName = htmlspecialchars_decode(resetSimpleQuote(utf8_decode($_POST['fileToConvert'])));
$filePath = "..".DIRECTORY_SEPARATOR.PATH_RAW_FILE.DIRECTORY_SEPARATOR."$fileName.log";

$logFile = MetierVideo::getLogAndProgress($filePath);
/*
$fileContent = file_get_contents($filePath, null, stream_context_create(array(
	'http' => array('header' => 'Accept-Charset: utf-8'))));
*/

$response = new AjaxResponseObject(AJAX_STATUS_OK, $logFile['fileContent'], 
		array(
				'duration' => $logFile['duration'],
				'time' => $logFile['time'],
				'progress' => $logFile['progress']));

echo json_encode_utf8($response);
exit;


?>