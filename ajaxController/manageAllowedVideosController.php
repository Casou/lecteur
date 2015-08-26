<?php

$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['action'])) {
	header('HTTP/1.1 400 Missing Parameter');
	echo "Paramètre 'action' non renseigné";
	exit;
}

Logger::init($pathToPhpRoot);

$action = $_POST['action'];
// parse_str($formulaire);

$ajaxReturnStatus = AJAX_STATUS_WARNING;
$ajaxReturnMessage = "Pas d'action correspondant à : $action";
$infos = null;

try {
	switch ($action) {
		case 'getVideosInfoForUsers' :
			$ids_video = $_POST['ids'];
			$users = MetierUser::getUserAllowedForVideo($ids_video);
			$ajaxReturnMessage = "Utilisateur récupérés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array('users' => $users);
			break;
		case 'saveAllowedVideosForUsers' :
			$ids_video = $_POST['ids_video'];
			$users = isset($_POST['users']) ? $_POST['users'] : null;
			MetierUser::saveUserAllowedForVideo($ids_video, $users);
			$ajaxReturnMessage = "Utilisateurs sauvegardés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		case 'getVideosInfoForProfiles' :
			$ids_video = $_POST['ids'];
			$profils = MetierProfil::getProfilesAllowedForVideo($ids_video);
			$ajaxReturnMessage = "Profils récupérés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array('profils' => $profils);
			break;
		case 'saveAllowedVideosForProfiles' :
			$ids_video = $_POST['ids_video'];
			$profils = isset($_POST['profiles']) ? $_POST['profiles'] : null;
			MetierProfil::saveProfilsAllowedForVideo($ids_video, $profils);
			$ajaxReturnMessage = "Profils sauvegardés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		case 'getTagInfoForVideos' :
			$ids_video = $_POST['ids'];
			$tags = MetierTag::getTagsForVideos($ids_video);
			$ajaxReturnMessage = "Tags récupérés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array('tags' => $tags);
			break;
		case 'saveAttachedTagsForVideos' :
			$ids_video = $_POST['ids_video'];
			$tags = isset($_POST['tags']) ? $_POST['tags'] : null;
			MetierTag::saveAttachedTagsForVideo($ids_video, $tags);
			$ajaxReturnMessage = "Tags sauvegardés";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
			
			
		default :
			break;
	}
} catch (Exception $e) {
	Database::rollback();
	$ajaxReturnStatus = AJAX_STATUS_KO;
	$ajaxReturnMessage = $e->getMessage();
}

$response = new AjaxResponseObject($ajaxReturnStatus, $ajaxReturnMessage, $infos);
echo Fwk::json_encode_utf8($response);
exit;

?>