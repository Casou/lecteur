<?php
$title = "Playlists";
include_once "entete.php";

$playlists_created = MetierPlaylist::getPlaylistCreatedByUser(CONNECTED_USER_ID);
$playlists_granted = MetierPlaylistUserRights::getPlaylistSharedWithUser(CONNECTED_USER_ID);

?>
<div id="div_playlist_list">
	<ul data-inset="true" data-role="listview">
		<li data-role="list-divider" role="heading">Créé par vous</li>
		<?php foreach($playlists_created as $playlist) { ?>
			<li>
				<a href="playlist_lecteur.php?id=<?= $playlist->id ?>">
					<?= $playlist->nom ?>
				</a>
			</li>
		<?php } ?>
	
	<?php if (count($playlists_granted) > 0) { ?>
		<li data-role="list-divider" role="heading">Visibles par vous</li>
		<?php foreach($playlists_granted as $playlistDTO) {
			$playlist = $playlistDTO->playlist;
			$can_edit = $playlistDTO->can_write || $playlistDTO->can_share;
			$classTitle = "";
			if ($playlistDTO->nbVideos == 0) {
		?>
			<li class="empty" title="Playlist vide">
				<?= $playlist->nom ?>
				<span class="playlist_creator">(<?= $playlistDTO->creator ?>)</span>
				-- Vide --
			</li>
		<?php
			} else { 
		?>
			<li>
				<a href="playlist_lecteur.php?id=<?= $playlist->id ?>">
					<?= $playlist->nom ?>
					<span class="playlist_creator">(<?= $playlistDTO->creator ?>)</span>
				</a>
			</li>
		<?php
			} 
		}
	}
		?>
	
	</ul>
</div>

<script>

</script>

<?php
include_once "pied.php";
?>