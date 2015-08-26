<?php
session_start();

header('Content-Type: text/html; charset=utf-8');

$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$id_folder = $_POST['id'];

$playlistFolder = MetierPlaylistFolder::getPlaylistFolderById($id_folder)[0];

$allAdmins = MetierUser::getAllAdminUser();
$userDto = MetierUser::getUserById($playlistFolder->id_user);
$allAdmins[] = $userDto->user;

$allAdminsLogin = array();
$allUsers = MetierUser::getAllUser();
$userRights = MetierPlaylistUserRights::getUserRights($id_folder, 'FOLDER');

include_once $pathToPhpRoot.'popupAllowUser.php';
include_once $pathToPhpRoot.'popupAllowProfile.php';

?>

<form id="folder_form" onSubmit="return false;">
	<input type="hidden" name="id_folder" id="id_folder" value="<?= $id_folder ?>" />
	<div id="playlist_folder_properties">
		<h2>Nom de la playlist</h2>
		<div>
			Modifier le nom de la playlist : <input type="text" maxlength="50" id="nom_folder" name="nom_folder" value="<?= Fwk::escapeDoubleQuote($playlistFolder->nom) ?>" />
		</div>
	</div>
	
	<div id="playlist_video_list_rights">
		<h2>Droits sur le dossier</h2>
		
		<div class="playlist_action_bar">
		</div>
		
		<table>
			<tr>
				<th>Utilisateur</th>
				<th>Lecture</th>
				<th><abbr title="Droit de lecture même sur les vidéos qui ne sont pas dans son périmètre">Lecture +</abbr></th>
			</tr>
			<?php 
			foreach ($allAdmins as $admin) { 
				// Pour éviter les doublons : admin ET propriétaire de la playlist
				if (!in_array($admin->login, $allAdminsLogin)) {
			?>
			<tr>
				<td class="playlist_rights_user"><?= $admin->login ?></td>
				<td class="playlist_rights_check"><input type="checkbox" name="read_check" checked="checked" disabled="disabled" /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="read_plus_check" checked="checked" disabled="disabled" /></td>
			</tr>
			<?php }
				$allAdminsLogin[] = $admin->login;
			}
			?>
			<?php 
			foreach ($allUsers as $user) { 
				if (!in_array($user->login, $allAdminsLogin)) {
					$check_can_read = "";
					$check_can_read_plus = "";
					if (isset($userRights[$user->id])) {
						$rights = $userRights[$user->id];
						$check_can_read = $rights->can_read ? 'checked="checked"' : "";
						$check_can_read_plus = $rights->can_read_plus ? 'checked="checked"' : "";
					}
			?>
			<tr>
				<td class="playlist_rights_user"><?= $user->login ?><input type="hidden" name="id_user[]" value="<?= $user->id ?>" /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="read_check_<?= $user->id ?>" <?= $check_can_read ?> /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="read_plus_check_<?= $user->id ?>" <?= $check_can_read_plus ?> /></td>
			</tr>
			<?php } 
			}
			?>
		</table>
	</div>
	
	<button id="save_folder_edit">Sauvegarder les modifications</button>
	<button id="delete_folder" class="red_button">Supprimer ce dossier</button>
	
</form>

<script>

	function saveFolder() {
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/managePlaylistController.php',
			dataType : 'json',
			async : false,
			data: {
				action : 'saveFolderPreferences',
				formulaire : $('#folder_form').serialize()
			},
			success: function(data, textStatus, jqXHR) {
				$('#folder_list_item_' + $('#id_folder').val()).html($('#nom_folder').val());
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
		hideLoadingPopup();
	}

	function deleteFolder() {
		if (!confirm('Etes-vous sûr de vouloir supprimer ce dossier ?')) {
			return;
		}
		
		showLoadingPopup();
		var id_folder = $('#id_folder').val();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/managePlaylistController.php',
			dataType : 'json',
			async : false,
			data: {
				action : 'deleteFolder',
				id_folder : id_folder
			},
			success: function(data, textStatus, jqXHR) {
				window.location.replace("<?= APPLICATION_ABSOLUTE_URL ?>playlist.php");
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
		
		// hideLoadingPopup();
	}

	
	
	$(document).ready(function() {
		
		$('#save_folder_edit').button( {
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			saveFolder();
		});
		
		$('#delete_folder').button( {
			icons: {
				primary: "ui-icon-trash"
			}
		}).click(function() {
			deleteFolder();
		});
		
	});

</script>

