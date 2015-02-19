<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

$playlists_created = MetierPlaylist::getPlaylistCreatedByUser($_SESSION['userId']);
$playlists_granted = MetierPlaylistUserRights::getPlaylistSharedWithUser($_SESSION['userId']);
?>
<div id="title">
	<h1>Playlists</h1>
</div>

<div id="liste_playlist">
	
	<div class="playlist_list">
		<div class="playlist_list_title">Créés par vous</div>
		<div class="playlist_list_ul">
			<ul>
			<?php foreach($playlists_created as $playlist_created) { ?>
				<li id="playlist_<?= $playlist_created->id ?>">
					<a href="#" onClick="showPlaylist(<?= $playlist_created->id ?>); return false;">
						<?= $playlist_created->nom ?>
					</a>
					<a class="playlist_list_edit" href="#" onClick="editPlaylist(<?= $playlist_created->id ?>); return false;">
						<img src="style/images/modify_mini.png" />
					</a>
				</li>
			<?php } ?>
			</ul>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
	
	<?php if (count($playlists_granted) > 0) { ?>
	<div class="playlist_list">
		<div class="playlist_list_title">Visibles par vous</div>
		<div class="playlist_list_ul">
			<ul>
				<?php foreach($playlists_granted as $playlistDTO) {
					$playlist = $playlistDTO->playlist;
					$can_edit = $playlistDTO->can_write || $playlistDTO->can_share;
					$classTitle = "";
					if ($playlistDTO->nbVideos == 0) {
				?>
					<li id="playlist_<?= $playlist->id ?>" class="empty" title="Playlist vide">
						<a href="#" onClick="return false;">
							<?= $playlist->nom ?>
							<span class="playlist_creator">(<?= $playlistDTO->creator ?>)</span>
						</a>
				<?php
					} else { 
				?>
					<li id="playlist_<?= $playlist->id ?>">
						<a href="#" onClick="showPlaylist(<?= $playlist->id ?>); return false;">
							<?= $playlist->nom ?>
							<span class="playlist_creator">(<?= $playlistDTO->creator ?>)</span>
						</a>
				<?php
					} 
				?>
						<?php if($can_edit) { ?>
						<a class="playlist_list_edit" href="#" onClick="editPlaylist(<?= $playlist->id ?>); return false;">
							<img src="style/images/modify_mini.png" />
						</a>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
	<?php } ?>

</div>


<div id="contenu_playlist">
</div>





<script>

function showPlaylist(id) {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'playlist_lecteur.php', 
		dataType : 'html',
		data: {
			id : id
		},
		success: function(data, textStatus, jqXHR) {
			$('#contenu_playlist').html(data);
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
		}
	});
}

function editPlaylist(id) {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'playlist_edit.php', 
		dataType : 'html',
		data: {
			id : id
		},
		success: function(data, textStatus, jqXHR) {
			$('#contenu_playlist').html(data);
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
		}
	});
}


</script>




<?php
include_once $pathToPhpRoot."pied.php";
?>
