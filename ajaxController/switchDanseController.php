<?php
$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

if (!isset($_POST['id_danse']) || !isset($_POST['id_user'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'fileToConvert' non renseigné";
	exit;
}

$id_danse = $_POST['id_danse'];
$id_user = $_POST['id_user'];
$switchOn = $_POST['switch_on'] == "true";

MetierDanse::switchOn($id_user, $id_danse, $switchOn);

$response = new AjaxResponseObject(AJAX_STATUS_OK, "Statut de la danse changé");
echo json_encode_utf8($response);
exit;


?>