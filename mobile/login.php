<?php
session_start();

$pathToPhpRoot = "../";
include_once $pathToPhpRoot.'fwk/php/includeFwk.php';

header('Content-Type: text/html; charset=utf-8');

if(isset($_GET['action'])) {
	$action = $_GET['action'];
	if ($action == "disconnect") {
		session_unset();
		unset($_COOKIE['forcePC']);
	}
}

$errorMessage = "";
if(isset($_GET['message'])) {
	$messageCode = $_GET['message'];
	
	switch ($messageCode) {
		case 1 :
			$errorMessage = "Login / Mot de passe invalide.";
			break;
		case 2 :
			$errorMessage = "Vous devez être connecté pour consulter le site.";
			break;
		default;
			break;
	}
}

$url = "index.php";
if(isset($_POST['url'])) {
	$url = $_POST['url'];
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Connexion - Mobile</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="style/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="style/themes/lecteur-mobile.min.css" />
	<link rel="stylesheet" href="style/themes/jquery.mobile.structure-1.4.0.min.css" />
	
	<link rel="stylesheet" href="style/style.css" />
	<link rel="stylesheet" href="style/styleLogin.css" />
	
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.mobile-1.4.0.min.js"></script>
	
	<script>
		$(document).ready(function() {
			$('#loginSubmit').click(function() {
				$('form').submit();
			});
		});
	</script>
	
</head>

<body id="loginPage">
	<?php
	if (Fwk::isUsingIE6OrLess()) { 
		$infoNavigateur = Fwk::getNavigateur();
		?>
		<h1>Ce site n'est pas compatible avec Internet Explorer 6 ou inférieur.</h1>
		<h2>Merci de télécharger un navigateur qui sert à quelque chose pour utiliser ce site...</h2>
		<p>Vous utilisez actuellement : <?= $infoNavigateur[1] ?></p>
		<p>
			Le site étant optimisé pour Firefox, nous vous conseillons de télécharger sa dernière version :
			<a href="http://www.mozilla.org/fr/firefox/" target="_blank">cliquez ici.</a>
		</p> 
	<?php } else { ?>
	
	<!-- 
	<div id="version">
		Version : <?= APPLICATION_VERSION ?>
	</div>
	 -->
	
	
	<div data-role="page" data-theme="a">
		<div data-role="header" data-position="inline">
			<h1>Lecteur vidéo</h1>
		</div>
		
		<form action="<?= $url ?>" method="post">
			<div data-role="content" data-theme="a">
				<div class="ui-body ui-body-a ui-corner-all" data-form="ui-body-a" data-theme="a">
					<table>
						<tr>
							<td><label for="loginInput">Login :</label></td>
							<td><input type="text" id="loginInput" name="login" /></td>
					</tr>
						<tr>
							<td><label for="passwordInput">Mot de passe :</label></td>
							<td><input type="password" id="passwordInput" name="password" /></td>
						</tr>
					</table>
				</div>
				
				<?php if ($errorMessage != "") { ?>
				<div class="ui-body ui-body-b ui-corner-all" data-form="ui-body-b" data-theme="b">
					<p><?= $errorMessage ?></p>
				</div>
				<?php } ?>
				
				<button id="loginSubmit">S'identifier</button>
			</div>
			
		</form>
		
		
		
	</div>
	
	
	<?php 
	/*
	if (!Fwk::isUsingFirefox()) { 
		$infoNavigateur = Fwk::getNavigateur(); 
	?>
	<div id="error_login" class="ui-state-error ui-corner-all">
		Ce site est optimisé pour <strong>Firefox</strong>. En utilisant un autre navigateur, 
		il se peut que certaines fonctionalités ne fonctionnent pas.<br/>
		Vous utilisez actuellement : <?= $infoNavigateur[1] ?> 
	</div>
	<?php } */?>

	<?php } ?>
</body>
</html>
