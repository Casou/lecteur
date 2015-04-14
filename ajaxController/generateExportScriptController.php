<?php
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

Logger::init($pathToPhpRoot);

header('Content-Type: text/html; charset=utf-8');


$exportMinId = $_POST['exportMinId'];
$exportMaxId = $_POST['exportMaxId'];
if (trim($exportMaxId) == "") {
	$exportMaxId = null;
}

$videos = MetierVideo::getVideoByIdBetweenBornes($exportMinId, $exportMaxId);

$scripts = "";
foreach($videos as $video) {
	$scripts .= "INSERT INTO ".Video::getTableName()."(nom_video, nom_affiche, duree, code_partage) VALUES ".
			"('".escapeString($video->nom_video)."',  '".escapeString($video->nom_affiche)."', $video->duree , ".
			"MD5(CONCAT($video->id, '".escapeString($video->nom_video)."', NOW())));\n";
	/*
	$scripts .= "UPDATE ".Video::getTableName()." SET duree = $video->duree WHERE SUBSTRING(nom_video,4) = ".
		"SUBSTRING('".escapeString($video->nom_video)."', 4);\n";
	*/
}


$response = new AjaxResponseObject(AJAX_STATUS_OK, $scripts);
echo json_encode_utf8($response);
exit;
?>