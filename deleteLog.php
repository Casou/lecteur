<?php 

$pathToPhpRoot = "./";
include_once $pathToPhpRoot."includes.php";

$handle=opendir($pathToPhpRoot.PATH_LOG_FOLDER);
while (false !== ($fichier = readdir($handle))) {
	if (!Fwk::startsWith($fichier, '.')) {
		unlink($pathToPhpRoot.PATH_LOG_FOLDER."/".$fichier);
	}
} 

?>