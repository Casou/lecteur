<?php
session_start();

$pathToPhpRoot = "../";
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

if (isset($_POST["login"]) && isset($_POST["password"])) {
	$user = MetierUser::login($_POST["login"], $_POST["password"]);
	if ($user != null) {
		$_SESSION["user"] = $_POST["login"];
		$_SESSION["userId"] = $user->user->id;
		foreach($user->droits as $droit) {
			$_SESSION[$droit->nom] = $droit->label;
		}
	} else {
		Fwk::redirect("login.php?message=1");
	}
}

if (!isset($_SESSION["user"])) {
	// Fwk::redirect("login.php?message=2");
	include 'loginRedirect.php';
	exit;
}

if (!isset($title)) {
	$title = "Lecteur vidéo";
}

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Cours et stages - Mobile</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="style/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="style/themes/lecteur-mobile.min.css" />
	<link rel="stylesheet" href="style/themes/jquery.mobile.structure-1.4.0.min.css" />
	
	<link rel="stylesheet" href="style/style.css" />
	<link rel="stylesheet" href="style/styleIndex.css" />
	<link rel="stylesheet" href="style/styleTable.css" />
	<link rel="stylesheet" href="style/styleDialog.css" />
	<link rel="stylesheet" href="style/styleListeEvts.css" />
	<link rel="stylesheet" href="style/styleRecherche.css" />
	<link rel="stylesheet" href="style/stylePlaylist.css" />
	
	<link rel="shortcut icon" HREF="../style/images/icone.png">
	
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.mobile-1.4.0.min.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.0.custom.min.js"></script>
	
</head>

<body>

<div id="loadingPopup">
	<img src="../style/images/loading_popup.gif" alt="Loading..." />
</div>

<div data-role="page" data-theme="a" id="divPage">
	<div data-role="header" data-position="inline" data-theme="a">
		<a href="index.php" data-form="ui-icon" title="Accueil" data-role="button" role="button"
			class="ui-btn-left ui-btn-corner-all ui-btn ui-icon-home ui-btn-icon-notext ui-shadow"> 
			Accueil 
		</a>
		<h1><?= $title ?></h1>
		<a id="diconnect" href="login.php?action=disconnect" data-form="ui-icon" title="Déconnexion" data-role="button" role="button"
			class="ui-btn-right ui-btn-corner-all ui-btn ui-icon-power ui-btn-icon-notext ui-shadow"> 
			Déconnexion 
		</a>
	</div>
	
	<div data-role="content" data-theme="a">