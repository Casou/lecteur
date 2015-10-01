<?php 

if (!isset($id_current_folder)) throw new Exception('playlist_panel : $id_current_folder is not setted');
if (!isset($mode)) throw new Exception('playlist_panel : $mode is not setted');

$dragDrop = ($mode == "CREATED");

if ($id_current_folder == "") {

	if ($mode == "CREATED") {
// 				echo "<li>1</li>";
		$playlist_folders = MetierPlaylistFolder::getPlaylistFolderCreatedByUser(CONNECTED_USER_ID);
		$playlist_items = MetierPlaylist::getPlaylistDTOCreatedByUser(CONNECTED_USER_ID);
	} else { // GRANTED
// 				echo "<li>2</li>";
		$playlist_folders = MetierPlaylistUserRights::getFolderDTOSharedWithUser(CONNECTED_USER_ID);
		$playlist_items = MetierPlaylistUserRights::getPlaylistDTOSharedWithUser(CONNECTED_USER_ID);
	}

} else {

	if ($mode == "CREATED") {
// 				echo "<li>3</li>";
		$playlist_folders = array();
		$playlist_items = MetierPlaylist::getPlaylistDTOCreatedByUser(CONNECTED_USER_ID, $id_current_folder);
	} else { // GRANTED
// 				echo "<li>4</li>";
		$playlist_folders = array();
		$playlist_items = MetierPlaylist::getPlaylistDTONotCreatedByUser(CONNECTED_USER_ID, $id_current_folder);
	}


}



foreach($playlist_folders as $playlistFolderDTO) { 
	$img_pl = "style/images/folder.png";
	$title_pl = "Ouvrir le dossier de playlists";
	
	$playlist_folder = $playlistFolderDTO->playlist;
	$can_edit = $playlistFolderDTO->can_write || $playlistFolderDTO->can_share;
	$creator = ($playlistFolderDTO->creator != null) ? "<span class=\"playlist_creator\">($playlistFolderDTO->creator)</span>" : "";
	
?>
	<li id="playlist_folder_<?= $playlist_folder->id ?>" folder_id="<?= $playlist_folder->id ?>" <?= $dragDrop ? 'class="droppable_folder"' : '' ?>>
		<a href="#" onClick="showFolder(<?= $playlist_folder->id ?>, '<?= $mode ?>', this); return false;"  title="<?= $title_pl ?>">
			<img src="<?= $img_pl ?>" />
			<span id="folder_list_item_<?= $playlist_folder->id ?>"><?= $playlist_folder->nom ?></span>
			<?= $creator ?>
		</a>
		<div class="playlist_action">
			<?php if($can_edit) { ?>
			<a class="playlist_list_edit" href="#" onClick="editFolder(<?= $playlist_folder->id ?>); return false;">
				<img src="style/images/modify_mini.png" />
			</a>
			<?php } ?>
		</div>
	</li>
<?php } ?>
<?php foreach($playlist_items as $playlistDTO) { 
	$img_pl = "style/images/video.png";
	$title_pl = "Visionner la playlist";
	
	$playlist = $playlistDTO->playlist;
	$can_edit = $playlistDTO->can_write || $playlistDTO->can_share;
	$creator = ($playlistDTO->creator != null) ? "<span class=\"playlist_creator\">($playlistDTO->creator)</span>" : "";
	
	if ($playlistDTO->nbVideos == 0) {
?>
	<li id="playlist_<?= $playlist->id ?>" playlist_id="<?= $playlist->id ?>" class="empty <?= $dragDrop ? 'draggable_item' : '' ?>">
		<a href="#" onClick="return false;">
			<img src="style/images/video.png" />
			<span class="nbVideos" title="Playlist vide ou aucune de ses vidéos n'est visible par vous"><?= $playlistDTO->nbVideos ?></span>
			<?= $playlist->nom ?>
			<?= $creator ?>
		</a>
<?php
	} else { 
?>
	<li id="playlist_<?= $playlist->id ?>" <?= $dragDrop ? 'class="draggable_item"' : '' ?> playlist_id="<?= $playlist->id ?>">
		<a href="#" onClick="showPlaylist(<?= $playlist->id ?>); return false;" title="<?= $title_pl ?>">
			<img src="style/images/video.png" />
			<span class="nbVideos" title="Cette playlist contient <?= $playlistDTO->nbVideos ?> <?= ($playlistDTO->nbVideos > 1) ? "videos" : "video" ?>"><?= $playlistDTO->nbVideos ?></span>
			<span class="nom_playlist">
				<?= $playlist->nom ?>
				<?= $creator ?>
			</span>
		</a>
<?php
	} 
?>	
		<div class="playlist_action">
			<?php if($can_edit) { ?>
			<a class="playlist_list_edit" href="#" onClick="editPlaylist(<?= $playlist->id ?>); return false;">
				<img src="style/images/modify_mini.png" />
			</a>
			<?php } ?>
		</div>
	</li>
<?php } ?>

