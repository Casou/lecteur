<?php 

$pathToPhpRoot = "./";
include_once $pathToPhpRoot."includes.php";
Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

header("Content-type: text/plain");

include Logger::getLogFilePath();
?>