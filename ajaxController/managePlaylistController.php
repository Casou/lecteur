<?php
session_start();

header('Content-Type: text/html; charset=utf-8');

$pathToPhpRoot = "../";

include_once $pathToPhpRoot."includes.php";
include_once "ajaxResponseObject.php";

Logger::init($pathToPhpRoot);

$action = $_POST['action'];

$ajaxReturnStatus = AJAX_STATUS_WARNING;
$ajaxReturnMessage = "Pas d'action correspondant à : $action";
$infos = null;

try {
	switch ($action) {
		case 'addVideoToPlaylist' :
			$id = MetierPlaylist::saveVideoToPlaylist($_POST['id_playlist'], $_POST['nom_playlist'], $_POST['ids']);
			$ajaxReturnMessage = "Playlist sauvegardée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array('id' => $id);
			break;
		case 'savePlaylistPreferences' :
			$formulaire = stripslashes($_POST['formulaire']);
			$id = MetierPlaylist::savePlaylistPreferences($formulaire);
			$ajaxReturnMessage = "Préférences de playlist sauvegardées";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array();
			break;
			
			
		case 'deleteVideoFromPlaylist' :
			$id_playlist = $_POST['id_playlist'];
			$id_video = $_POST['id_video'];
			MetierPlaylist::deleteVideoFromPlaylist($id_playlist, $id_video);
			$ajaxReturnMessage = "Vidéo supprimée de la playlist";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array();
			break;
		case 'deletePlaylist' :
			$id_playlist = $_POST['id_playlist'];
			MetierPlaylist::deletePlaylist($id_playlist);
			$ajaxReturnMessage = "Playlist supprimée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			$infos = array();
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
echo json_encode_utf8($response);
exit;

?>