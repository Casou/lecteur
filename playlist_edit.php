<?php
session_start();

header('Content-Type: text/html; charset=utf-8');

$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$id_playlist = $_POST['id'];

$playlistDTO = MetierPlaylist::getPlaylistWithVideo(CONNECTED_USER_ID, $id_playlist);
$allAdmins = MetierUser::getAllAdminUser();
$userDto = MetierUser::getUserById($playlistDTO->playlist->id_user);
$allAdmins[] = $userDto->user;
$allAdminsLogin = array();
$allUsers = MetierUser::getAllUser();
$userRights = MetierPlaylistUserRights::getUserRights($id_playlist);

include_once $pathToPhpRoot.'popupAllowUser.php';
include_once $pathToPhpRoot.'popupAllowProfile.php';

?>

<form id="playlist_form">
	<input type="hidden" name="id_playlist" id="id_playlist" value="<?= $id_playlist ?>" />
	<?php if ($playlistDTO->can_write) { ?>
	<div id="playlist_video_list_order">
		<h2>Ordre de la playlist</h2>
		
		<div class="playlist_action_bar">
			<?php if(isset($_SESSION[DROIT_ACTION_ALLOW_USER])) { ?>
			<input type="button" onClick="popupUser(); return false;" value="Affecter tout à un utilisateur" title="Rendre toutes les vidéos de la playlist visible pour un utilisateur" />
			<?php } ?>
			<?php if(isset($_SESSION[DROIT_ACTION_ALLOW_PROFILE])) { ?>
			<input type="button" onClick="popupProfile(); return false;" value="Affecter tout à un profil" title="Rendre toutes les vidéos de la playlist visible pour un profil" />
			<?php } ?>
			<input type="button" onClick="deletePlaylist(); return false;" value="Supprimer" style="background-color : #FFDADA;" />
		</div>
		
		<ul id="playlist_video_list">
			<?php foreach ($playlistDTO->videos as $videoDTO) {
					$video = $videoDTO->video;
					$evenement = $videoDTO->evenement;
					$thumbnail = 'style/images/thumbnail.jpg';
					if (file_exists("ressources/thumbnails/$video->nom_video.jpg")) {
						$thumbnail = "ressources/thumbnails/$video->nom_video.jpg";
					}
			?>
			<li id="playlist_video_<?= $video->id ?>" class="playlist_video_li">
				<span class="ui-icon ui-icon-arrow-4"></span>
				
				<span class="video_delete">
					<a href="#" onClick="deleteVideoFromPlaylist(<?= $id_playlist ?>, <?= $video->id ?>); return false;"><img src="style/images/delete_cross.png" /></a>
				</span>
				
				<span class="video_thumbnail"><img src="<?= $thumbnail ?>" /></span>
				
				<span class="video_title"><?= $video->nom_affiche ?></span><br/>
				<span class="video_evenement"><?= $evenement->nom ?></span>
				
				<input type="hidden" class="hidden_video_id" name="idvideo[]" value="<?= $video->id ?>" />
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
	
	<?php if ($playlistDTO->can_share) { ?>
	<div id="playlist_video_list_rights">
		<h2>Droits sur la playlist</h2>
		
		<table>
			<tr>
				<th>Utilisateur</th>
				<th>Lecture</th>
				<th><abbr title="Droit de lecture même sur les vidéos qui ne sont pas dans son périmètre">Lecture +</abbr></th>
				<th>Modification</th>
				<th>Paramètre<br/>de partage</th>
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
				<td class="playlist_rights_check"><input type="checkbox" name="write_check" checked="checked" disabled="disabled" /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="share_check" checked="checked" disabled="disabled" /></td>
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
					$check_can_write = "";
					$check_can_share = ""; 
					if (isset($userRights[$user->id])) {
						$rights = $userRights[$user->id];
						$check_can_read = $rights->can_read ? 'checked="checked"' : "";
						$check_can_read_plus = $rights->can_read_plus ? 'checked="checked"' : "";
						$check_can_write = $rights->can_write ? 'checked="checked"' : "";
						$check_can_share = $rights->can_share ? 'checked="checked"' : "";
					}
			?>
			<tr>
				<td class="playlist_rights_user"><?= $user->login ?><input type="hidden" name="id_user[]" value="<?= $user->id ?>" /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="read_check_<?= $user->id ?>" <?= $check_can_read ?> /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="read_plus_check_<?= $user->id ?>" <?= $check_can_read_plus ?> /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="write_check_<?= $user->id ?>" <?= $check_can_write ?> /></td>
				<td class="playlist_rights_check"><input type="checkbox" name="share_check_<?= $user->id ?>" <?= $check_can_share ?> /></td>
			</tr>
			<?php } 
			}
			?>
		</table>
	</div>
	<?php } ?>
</form>

<button id="save_playlist_edit">Sauvegarder les modifications</button>

<script>
	function popupUser() {
		openAllowUserDialog($('.hidden_video_id'));
	}
	
	function popupProfile() {
		openAllowProfileDialog($('.hidden_video_id'));
	}

	function deleteVideoFromPlaylist(id_playlist, id_video) {
		if (!confirm('Etes-vous sûr de vouloir supprimer cette vidéo ?')) {
			return;
		}
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/managePlaylistController.php',
			dataType : 'json',
			async : false,
			data: {
				action : 'deleteVideoFromPlaylist',
				id_playlist : id_playlist,
				id_video : id_video
			},
			success: function(data, textStatus, jqXHR) {
				$('#playlist_video_' + id_video).remove();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
		hideLoadingPopup();
	}

	function savePlaylist() {
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/managePlaylistController.php',
			dataType : 'json',
			async : false,
			data: {
				action : 'savePlaylistPreferences',
				formulaire : $('#playlist_form').serialize()
			},
			success: function(data, textStatus, jqXHR) {
				
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
		hideLoadingPopup();
	}

	function deletePlaylist() {
		if (!confirm('Etes-vous sûr de vouloir supprimer cette playlist ?')) {
			return;
		}
		
		showLoadingPopup();
		var id_playlist = $('#id_playlist').val();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/managePlaylistController.php',
			dataType : 'json',
			async : false,
			data: {
				action : 'deletePlaylist',
				id_playlist : id_playlist
			},
			success: function(data, textStatus, jqXHR) {
				$('#playlist_' + id_playlist).remove();
				$('#contenu_playlist').html("");
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
		
		hideLoadingPopup();
	}

	
	
	$(document).ready(function() {
		$( "#playlist_video_list" ).sortable();

		$('#save_playlist_edit').button( {
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			savePlaylist();
		});

		

		$('#playlist_new_user_rights').button( {
			icons: {
				primary: "ui-icon-plusthick"
			}
		}).click(function() {
			
		});

	});

</script>

