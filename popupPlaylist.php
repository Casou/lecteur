<?php
$playlists = MetierPlaylist::getAllPlaylistAllowed($_SESSION['userId']);
$playlist_created = array();
$playlist_allowed = array();
$id_user_map = array();
foreach($playlists as $playlist) {
	if ($playlist->id_user == $_SESSION['userId']) {
		$playlist_created[] = $playlist;
	} else {
		if (!isset($id_user_map[$playlist->id_user])) {
			$id_user_map[$playlist->id_user] = MetierUser::getUserById($playlist->id_user);
		}
		$user = $id_user_map[$playlist->id_user];
		$user = $user->user;
		$playlist->nom .= " ($user->login)";
		$playlist_allowed[] = $playlist;
	}
}
?>

<div id="usePlaylistDialog" style="display : none" title="Affecter des vidéos à une playlist">
	<table>
		<tr>
			<th>
				Créer une playlist
			</th>
			<th>
				Ajouter à une playlist
			</th>
		</tr>
		<tr>
			<td>
				<input type="text" id="newPlaylistInput" name="newPlaylist" onKeyPressed="" 
					placeholder="Nom de la nouvelle playlist" />
			</td>
			<td>
				<select id="playlist_select">
					<option value="">---</option>
					<?php foreach($playlist_created as $playlist) { ?>
					<option value="<?= $playlist->id ?>"><?= $playlist->nom ?></option>
					<?php 
					}
					 
					if (count($playlist_created) > 0 && count($playlist_allowed) > 0) { ?>
					<option value="" disabled="disabled">---</option>
					<?php 
					}
					
					
					foreach($playlist_allowed as $playlist) {?>
					<option value="<?= $playlist->id ?>"><?= $playlist->nom ?></option>
					<?php 
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	
	<button>Sauvegarder</button>
</div>

<script>
	var id_video_playlist;

	function openPlaylistDialog(ids) {
		// console.log(ids);
		showLoadingPopup();
		
		var ids_array = new Array();
		$(ids).each(function() {
			ids_array.push($(this).val());
		});

		if (ids_array.length == 0) {
			alert("Veuillez sélectionnner au moins une vidéo;");
			hideLoadingPopup();
			return;
		}
		
		id_video_playlist = ids_array;
		$('#usePlaylistDialog').dialog('open');
		hideLoadingPopup();
	}


	function savePlaylist() {
		showLoadingPopup();
		id_playlist = $('#playlist_select').val();
		nom_playlist = $('#newPlaylistInput').val();
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/managePlaylistController.php',
			dataType : 'json',
			data: {
				action : 'addVideoToPlaylist',
				ids : id_video_playlist,
				id_playlist : id_playlist,
				nom_playlist : nom_playlist
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				$('#usePlaylistDialog').dialog('close');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		hideLoadingPopup();
	}


	

	$(document).ready(function() {
		$('#usePlaylistDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 200,
			resizable : false
		});

		$("#usePlaylistDialog button").button({
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			savePlaylist();
		});

	});

</script>