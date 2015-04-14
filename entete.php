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
	
	<link rel="stylesheet" href="style/jquery-ui-1.10.0.custom/css/redmond/jquery-ui-1.10.0.custom.css" />
	<link rel="stylesheet" href="style/html5demos.css" />
	<link rel="stylesheet" href="style/style.css" />
	<link rel="stylesheet" href="style/styleDatatable.css" />
	<link rel="stylesheet" href="style/styleTaille.css" />
	<link rel="stylesheet" href="style/stylePlayer.css" />
	<link rel="stylesheet" href="style/styleAdmin.css" />
	<link rel="stylesheet" href="style/stylePlaylist.css" />
	
	<link rel="shortcut icon" HREF="style/images/icone.png">
	
	<script type="text/javascript" src="style/jquery-ui-1.10.0.custom/js/jquery-1.9.0.js"></script>
	<script type="text/javascript" src="style/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables-1.9.4.min.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/h5utils.js"></script>
	<script type="text/javascript" src="js/jquery.srt.js"></script>
	<script type="text/javascript" src="js/globalize.js"></script>
	<script type="text/javascript" src="js/globalize.culture.fr-FR.js"></script>
	
	<?php if (isset($_SESSION[DROIT_LOG_AS]) || $_SESSION["userLogged"] != $_SESSION["userId"]) { ?>
	<script type="text/javascript" src="js/logAs.js"></script>
	<?php } ?>

	<script>
		var niveauLibelle = {
		<?php foreach($NIVEAUX as $niveau => $libelle) { ?>
			'<?= $niveau ?>' : '<?= $libelle ?>',
		<?php } ?>
		};
	</script>
	
</head>

<body>

<?php include "menu.php"; ?>

<div id="loadingPopup">
	<img src="style/images/loading_popup.gif" alt="Loading..." />
</div>

<div id="bodyContent">

