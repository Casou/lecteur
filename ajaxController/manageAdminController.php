<?php
session_start();

$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['formulaire'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'formulaire' non renseigné";
	exit;
}

Logger::init($pathToPhpRoot);

$formulaire = $_POST['formulaire'];
parse_str($formulaire);

if (!isset($action)) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'action' non renseigné";
	exit;
}

$ajaxReturnStatus = AJAX_STATUS_WARNING;
$ajaxReturnMessage = "Pas d'action correspondant à : $action";
$infos = null;

try {
	switch ($action) {
		case 'editUser' :
			$id = MetierUser::saveUser($formulaire);
			$ajaxReturnMessage = "Utilisateur sauvegardé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array('id' => $id);
			break;
		case 'deleteUser' :
			MetierUser::deleteUser($id);
			$ajaxReturnMessage = "Utilisateurs supprimé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		
		case 'editProfil' :
			$id = MetierProfil::saveProfil();
			$ajaxReturnMessage = "Profil sauvegardé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array('id' => $id);
			break;
		case 'deleteProfil' :
			MetierProfil::deleteProfil($id);
			$ajaxReturnMessage = "Profil supprimé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		case 'generateTmp' :
			MetierCritere::calculateAllowedVideosForAllUsers();
			$ajaxReturnMessage = "Tables TMP recalculées";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		
		case 'generateTmpForUser' :
			$user = $_POST['id_user'];
			MetierCritere::calculateAllowedVideos($user);
			$ajaxReturnMessage = "Tables TMP recalculées";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		case 'getAllUsers' :
			MetierCritere::calculateAllowedVideosForAllUsers();
			$ajaxReturnMessage = "Utilisateurs récupérés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		case 'logAs' :
			MetierUser::logAs($id);
			$ajaxReturnMessage = "Utilisateur changé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
			
		case 'deleteLog' :
			$todays_log = isset($todays_log) ? $todays_log = true : $todays_log = false; 
			if (!isset($userToPurge)) {
				$userToPurge = array();
			}
			MetierLog::deleteLog($todays_log, $userToPurge);
			$ajaxReturnMessage = "Log supprimés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'saveDefaultLogLevel' :
			MetierLog::changeDefaultLogLevel($default_log_level);
			$ajaxReturnMessage = "Niveau de log changé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		default :
			break;
	}
} catch (Exception $e) {
	Logger::error($e->getMessage());
	Database::rollback();
	$ajaxReturnStatus = AJAX_STATUS_KO;
	$ajaxReturnMessage = $e->getMessage();
}

$response = new AjaxResponseObject($ajaxReturnStatus, $ajaxReturnMessage, $infos);
echo Fwk::json_encode_utf8($response);
exit;

?>