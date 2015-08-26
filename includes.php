<?php

if (!isset($pathToPhpRoot)) {
	throw new Exception("pathToPhpRoot not defined", 500);
}

include_once $pathToPhpRoot."constantsPrivate.php";
include_once $pathToPhpRoot."constants.php";
include_once $pathToPhpRoot."database/database.php";
include_once $pathToPhpRoot."metier/includeMetier.php";
include_once $pathToPhpRoot."database/dto/includeDto.php";

include_once $pathToPhpRoot."fonctions.php";
$databaseClass = "Database";
include_once $pathToPhpRoot."fwk/php/includeFwk.php";
include_once $pathToPhpRoot."logger.php";

?>