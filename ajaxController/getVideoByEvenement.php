<?php
session_start();
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['evenement']) || !isset($_POST['danse'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'evenement' ou 'danse' non renseigné";
	exit;
}

Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$id_evenement = $_POST['evenement'];
$id_danse = $_POST['danse'];
$id_user = isset($_POST['id_user']) ? $_POST['id_user'] : $_SESSION['userId'];

$videos = MetierVideo::getVideoByDanseAndEvenementWithAttributes($id_danse, $id_evenement, $id_user);

include $pathToPhpRoot."liste_tableauAjax.php";
?>