<?php
session_start();
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['prof']) || !isset($_POST['danse'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'prof' ou 'danse' non renseigné";
	exit;
}

Logger::init($pathToPhpRoot);

$id_prof = $_POST['prof'];
$id_danse = $_POST['danse'];

$videos = MetierVideo::getVideoByDanseAndProfWithAttributes($id_danse, $id_prof);

include $pathToPhpRoot."liste_tableauAjax.php";
?>