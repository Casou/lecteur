<?php 
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$id_current_folder = (isset($_POST['id_folder'])) ? $_POST['id_folder'] : null;
$mode = (isset($_POST['mode'])) ? $_POST['mode'] : "CREATED";

include_once('playlist_panel.php');

?>