<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

include_once "includes.php";
Logger::init($pathToPhpRoot);

if (isset($_GET["action"])) {
	$action = $_GET["action"];
	switch ($action) {
		case "forcePC" :
			setcookie("forcePC", 1, time() + (365*24*3600), "/".APPLICATION_URL);
		default;
		break;
	}
}

// Redirection vers la version mobile
require $pathToPhpRoot."tools/Mobile-Detect/Mobile_Detect.php";
$detect = new Mobile_Detect();
$isPhone = $detect->isMobile() && !$detect->isTablet();

if ($isPhone && !isset($_COOKIE["forcePC"])) {
	Fwk::redirect("mobile/");
	exit;
}



if (isset($_POST["login"]) && isset($_POST["password"])) {
	$user = MetierUser::login($_POST["login"], $_POST["password"]);
	Logger::reinit($pathToPhpRoot);
	Logger::info("---------------------- Nouvelle session : ".Fwk::getIp()." ----------------------");
	Logger::info("------------------------- ".date('d/m/y H:i:s')." -------------------------");
	MetierLog::addConnexion($_POST["login"]);
	if ($user == null) {
		Fwk::redirect("login.php?message=1");
	}
}

if (!isset($_SESSION["user"])) {
	// Fwk::redirect("login.php?message=2");
	include 'loginRedirect.php';
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Cours et stages</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/jquery-ui-1.10.0.custom/css/redmond/jquery-ui-1.10.0.custom.css" />
	<!-- <link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/html5demos.css" /> -->
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/style.css" />
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/styleDatatable.css" />
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/styleTaille.css" />
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/stylePlayer.css" />
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/styleAdmin.css" />
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/stylePlaylist.css" />
	
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>style/jquery-ui-1.10.0.custom/js/jquery-1.9.0.js"></script>
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>style/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.js"></script>
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/jquery.dataTables-1.9.4.min.js"></script>
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/common.js"></script>
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/h5utils.js"></script>
	<!-- <script type="text/javascript" src="js/jquery.srt.js"></script> -->
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/globalize.js"></script>
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/globalize.culture.fr-FR.js"></script>
	
	<?php if (isset($_SESSION[DROIT_LOG_AS]) || $_SESSION["userLogged"] != $_SESSION["userId"]) { ?>
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/logAs.js"></script>
	<?php } ?>
	
	
	<link rel="stylesheet" href="<?= APPLICATION_ABSOLUTE_URL ?>style/jwskin-lecteur.css" />
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>tools/jwplayer/jwplayer_7.js"></script>
	
	<script type="text/javascript" src="<?= APPLICATION_ABSOLUTE_URL ?>js/tinymce/tinymce.min.js"></script>
	
	<?php 
		$var_theme = 'THEME_'.$_SESSION['theme'];
		echo $$var_theme;
	?>

	<script>
		var niveauLibelle = {
		<?php foreach($NIVEAUX as $niveau => $libelle) { ?>
			'<?= $niveau ?>' : '<?= $libelle ?>',
		<?php } ?>
		};
	</script>
	
	
</head>

<body>

<div id="loadingPopup">
	<img src="<?= APPLICATION_ABSOLUTE_URL ?>style/images/loading_popup.gif" alt="Loading..." />
</div>

<div id="bodyWrap">
	<header>
		<div id="images_header">
			<div id="images_header_1"></div>
			<div id="images_header_2"></div>
			<div id="images_header_3"></div>
			<div id="images_header_4"></div>
			<div id="images_header_5"></div>
		</div>
	
	<?php include "menu.php"; ?>
	</header>
	
	<div id="bodyContent">
