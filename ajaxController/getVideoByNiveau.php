<?php
session_start();
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['niveau']) || !isset($_POST['danse'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'evenement' ou 'danse' non renseigné";
	exit;
}

Logger::init($pathToPhpRoot);

$niveau = $_POST['niveau'];
$id_danse = $_POST['danse'];

$videos = MetierVideo::getVideoByDanseAndNiveauWithAttributes($id_danse, $niveau);
$manageNiveau = true;

include $pathToPhpRoot."liste_tableauAjax.php";
?>