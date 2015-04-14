<?php 
session_start();

/* ---------------------------------------------------
 * ---*********************************************---
 * ---*********************************************---
 * ---***                                       ***---
 * ---***    Fichier PHP encod� en ISO8859-1    ***--- 
 * ---***     pour la compatibilit� Windows     ***---
 * ---***                                       ***---
 * ---*********************************************--
 * ---*********************************************--- 
 * --------------------------------------------------- */


if (!isset($_GET["user_login"])) {
	throw new Exception("Requ�te incompl�te");
}
$user_login = $_GET["user_login"];

$pathToPhpRoot = "./";
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

header("Content-type: text/plain;");

$logFile = MetierLog::getLastLog($user_login);

if ($logFile == null) {
	echo "Pas de fichier de log trouv�";
} else {
	echo "[Fichier : $logFile]\n";
	echo "-------------------------------------------------------------------------------------------------\n";
	echo "-------------------------------------------------------------------------------------------------\n\n";
	include $logFile;
}

?>