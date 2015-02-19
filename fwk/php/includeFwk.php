<?php

if (!isset($pathToPhpRoot)) {
	$pathToPhpRoot = ".";
}
if (!isset($pathToFwkFiles)) {
	$pathToFwkFiles = "fwk/php";
}

include_once($pathToPhpRoot."/".$pathToFwkFiles.'/modele/interfaceDo.php');
include_once($pathToPhpRoot."/".$pathToFwkFiles.'/modele/DoParameter.php');
include_once($pathToPhpRoot."/".$pathToFwkFiles.'/modele/DoVersion.php');

include_once($pathToPhpRoot."/".$pathToFwkFiles.'/metier/fwk.php');
include_once($pathToPhpRoot."/".$pathToFwkFiles.'/metier/fwkParameter.php');
include_once($pathToPhpRoot."/".$pathToFwkFiles.'/metier/logger.php');


?>