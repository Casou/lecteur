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

$formulaire = stripslashes($_POST['formulaire']);
parse_str($formulaire);

// if (!isset($_POST['action'])) {
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
		case 'addProfesseur':
			$newId = MetierProfesseur::insertProfesseur(stripslashes($nom));
			$ajaxReturnMessage = "Professeur ajouté";
			$infos = array('id' => $newId);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'changeNomProfesseur':
			$newId = MetierProfesseur::updateNomProfesseur($id, stripslashes($nom));
			$ajaxReturnMessage = "Professeur modifié";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'removeProfesseur':
			$newId = MetierProfesseur::deleteProfesseur($id);
			$ajaxReturnMessage = "Professeur supprimé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'getAllProfesseursJs' :
			$allProfesseurs = MetierProfesseur::getAllProfesseur();
			$ajaxReturnMessage = "Toutes les professeurs ont été récupérées dans la variable infos['professeurs']";
			$infos = array('professeurs' => $allProfesseurs);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
			
		case 'addDanse':
			$newId = MetierDanse::insertDanse(stripslashes($nom));
			$ajaxReturnMessage = "Danse ajoutée";
			$infos = array('id' => $newId);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'changeNomDanse':
			$newId = MetierDanse::updateNomDanse($id, stripslashes($nom));
			$ajaxReturnMessage = "Danse modifiée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'removeDanse':
			$newId = MetierDanse::deleteDanse($id);
			$ajaxReturnMessage = "Danse supprimée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'getAllDanses':
			$allDanses = MetierDanse::getAllDanse();
			$ajaxReturnMessage = "Toutes les danses ont été récupérées dans la variable infos['danses']";
			$infos = array('danses' => $allDanses);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
			
		case 'addEvenement':
			$do = MetierEvenement::insertEvenement(stripslashes($nom), $date, stripslashes($ville), $couleur);
			$ajaxReturnMessage = "Evenement ajouté";
			$infos = array('evenement' => $do);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'changeEvenement':
			$newId = MetierEvenement::updateEvenement($id, stripslashes($nom), $date, stripslashes($ville), $couleur);
			$ajaxReturnMessage = "Evenement modifié";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'removeEvenement':
			$newId = MetierEvenement::deleteEvenement($id);
			$ajaxReturnMessage = "Evenement supprimé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'getAllEvenementsJs' :
			$allEvenements = MetierEvenement::getAllEvenement(true);
			$ajaxReturnMessage = "Toutes les évènements ont été récupérées dans la variable infos['evenement']";
			$infos = array('evenements' => $allEvenements);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
			
		case 'getVideoWithPasses' :
			$videoDto = MetierVideo::getVideoWithPasses($id);
			$ajaxReturnMessage = "Vidéo récupérée";
			$infos = array('video' => $videoDto);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'checkProfileAffected' :
			$critere = MetierCritere::recoverCritereFromFormulaire($formulaire);
			$profils = MetierProfil::getProfilByCritere($critere);
			$ajaxReturnMessage = "Profils récupérés";
			$infos = array('profils' => $profils);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'saveVideoProperties' :
			MetierVideo::saveVideoProperties($formulaire);
			$ajaxReturnMessage = "Propriétés de la vidéo enregistrées";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'deleteVideo' :
			MetierVideo::deleteVideo($id);
			$ajaxReturnMessage = "Vidéo supprimée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'deleteRawVideo' :
			MetierVideo::deleteRawVideo($nom);
			$ajaxReturnMessage = "Vidéo supprimée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'deleteBinVideo' :
			MetierVideo::deleteBinVideo($nom);
			$ajaxReturnMessage = "Vidéo supprimée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'moveBinVideo' :
			MetierVideo::moveBinVideo($nom);
			$ajaxReturnMessage = "Vidéo déplacée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'changeNomAfficheVideo' : 
			MetierVideo::changeNomAfficheVideo($id, $nom);
			$ajaxReturnMessage = "Vidéo renommée";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'addFavori' :
			MetierVideo::changeFavori($videoId, 'addFavori');
			$ajaxReturnMessage = "Favori ajouté";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'removeFavori' :
			MetierVideo::changeFavori($videoId, 'removeFavori');
			$ajaxReturnMessage = "Favori retiré";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
		case 'addTag':
			$newId = MetierTag::insertTag(stripslashes($label));
			$ajaxReturnMessage = "Tag ajouté";
			$infos = array('id' => $newId);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'changeLabelTag':
			$newId = MetierTag::updateLabelTag($id, stripslashes($label));
			$ajaxReturnMessage = "Tag modifié";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'removeTag':
			$newId = MetierTag::deleteTag($id);
			$ajaxReturnMessage = "Tag supprimé";
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
		case 'getAllTagsJs' :
			$allTags = MetierTag::getAllTag();
			$ajaxReturnMessage = "Tous les tags ont été récupérés dans la variable infos['tags']";
			$infos = array('tags' => $allTags);
			$ajaxReturnStatus = AJAX_STATUS_OK;
			break;
			
			
			
		case 'updateTabOrder' :
			MetierDanse::saveDanseOrderForUser($ids, CONNECTED_USER_ID);
			$ajaxReturnMessage = "L'ordre des onglets a été sauvegardé";
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

if ($ajaxReturnStatus == AJAX_STATUS_OK) {
	header('HTTP/1.1 200 OK');
} else {
	header('HTTP/1.1 500 Server problem');
}

$response = new AjaxResponseObject($ajaxReturnStatus, $ajaxReturnMessage, $infos);
echo Fwk::json_encode_utf8($response);
exit;

?>