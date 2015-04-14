<?php
session_start();

$pathToPhpRoot = "../../";

include_once $pathToPhpRoot."includes.php";
include_once $pathToPhpRoot."ajaxController/ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

//Logger::init("../");

$id = $_POST['id'];

$ajaxReturnStatus = AJAX_STATUS_WARNING;
$ajaxReturnMessage = "Warning init";
$infos = null;

try {
	
	$videoDto = MetierVideo::getVideoWithPasses($id);
	$ajaxReturnMessage = "Vidéo récupérée";
	$infos = array('video' => $videoDto);
	$ajaxReturnStatus = AJAX_STATUS_OK;
			
			
} catch (Exception $e) {
	Database::rollback();
	$ajaxReturnStatus = AJAX_STATUS_KO;
	$ajaxReturnMessage = $e->getMessage();
}

if ($ajaxReturnStatus == AJAX_STATUS_OK) {
	header('HTTP/1.1 200 OK');
} else {
	header('HTTP/1.1 500 Server problem');
}

$response = new AjaxResponseObject($ajaxReturnStatus, $ajaxReturnMessage, $infos);
echo json_encode_utf8($response);
exit;

?>