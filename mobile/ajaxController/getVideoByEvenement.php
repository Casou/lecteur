<?php
session_start();
$pathToPhpRoot = "../../";

include_once $pathToPhpRoot."includes.php";
include_once $pathToPhpRoot."ajaxController/ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['evenement']) || !isset($_POST['danse'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'evenement' ou 'danse' non renseigné";
	exit;
}

$id_evenement = $_POST['evenement'];
$id_danse = $_POST['danse'];

$videos = MetierVideo::getVideoByDanseAndEvenementWithAttributes($id_danse, $id_evenement, $_SESSION['userId']);

include "../liste_tableauAjax.php";
?>