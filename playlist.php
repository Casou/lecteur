<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

/*
$playlists_created = MetierPlaylist::getPlaylistCreatedByUser($_SESSION['userId']);
$playlists_folder_created = MetierPlaylistFolder::getPlaylistFolderCreatedByUser($_SESSION['userId']);
*/


// $playlists_granted = MetierPlaylistUserRights::getPlaylistDTOSharedWithUser($_SESSION['userId']);
?>
<div id="title">
	<h1>Playlists</h1>
</div>

<div id="liste_playlist">
	
	<button id="addFolder">Créer un dossier</button>
	<button id="addPlaylistButton">Créer une playlist</button>
	
	<div id="createFolder" title="Créer un nouveau dossier de playlists" style="display : none;" class="dialog_input">
		<div>
			<label for="createFolderName">Nom du dossier : </label>
			<input id="createFolderName" maxlength="50" />
		</div>
		
		<button id="createFolderButton">Créer le dossier</button>
	</div>
	
	<div id="createPlaylist" title="Créer une playlist vide" style="display : none;" class="dialog_input">
		<div>
			<label for="createPlaylistName">Nom de la playlist : </label>
			<input id="createPlaylistName" maxlength="50" />
		</div>
		
		<button id="createPlaylistButton">Créer la playlist</button>
	</div>
	
	<div id="popup_playlist"></div>
	
	
	
	
	
	
	<div id="playlist_created_by_me" class="playlist_list">
		<?php 
			$id_current_folder = "";
			$mode = "CREATED";
		?>
		<div class="playlist_list_title" onClick="collapse('playlist_created_by_me')">Crées par moi</div>
		<div class="playlist_list_content">
			<div class="playlist_list_ul playlists">
				<ul>
				<?php 
					include ('playlist_panel.php');
				?>
				</ul>
			</div>

			<div class="playlist_list_ul folder_detail" style="display : none">
				<ul>
					<li folder_id="null" class="droppable_folder folder_up">
						<a href="#" onClick="hideFolder('<?= $mode ?>', this); return false;">
							<img src="style/images/folder_up.png" />
							Retour
						</a>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="clear">&nbsp;</div>
	</div>
	
	
	
	
	
	<?php
		$id_current_folder = "";
		$mode = "GRANTED";
	?>
	<div id="playlist_visible_by_me" class="playlist_list">
		<div class="playlist_list_title" onClick="collapse('playlist_visible_by_me')">Visibles par moi</div>
		<div class="playlist_list_content">
			<div class="playlist_list_ul playlists">
				<ul>
					<?php 
						include ('playlist_panel.php');
					?>
				</ul>
			</div>
			
			<div class="playlist_list_ul folder_detail" style="display : none">
				<ul>
					<li folder_id="null" class="droppable_folder folder_up">
						<a href="#" onClick="hideFolder('<?= $mode ?>', this); return false;">
							<img src="style/images/folder_up.png" />
							Retour
						</a>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="clear">&nbsp;</div>
	</div>

</div>


<div id="contenu_playlist">
</div>





<script>

function collapse(id) {
	$('#' + id + ' .playlist_list_content').toggle("blind");
}

function showPlaylist(id) {
	if (isDragging) {
		return false;
	}
	
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'playlist_lecteur.php', 
		dataType : 'html',
		async : false,
		data: {
			id : id
		},
		success: function(data, textStatus, jqXHR) {
			$('#contenu_playlist').html(data).css('margin-left', '0');
			$('#liste_playlist').hide();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
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


function createPlaylistFolder() {
	var nom_playlist_folder = $('#createFolderName').val().trim();
	if (nom_playlist_folder == "") {
		alert('Saisissez un nom de dossier');
		return false;
	}

	showLoadingPopup();
	
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/managePlaylistController.php',
		dataType : 'json',
		async : false,
		data: {
			action : 'addPlaylistFolder',
			nom_playlist_folder : nom_playlist_folder
		},
		success: function(data, textStatus, jqXHR) {
			window.location.replace('<?= APPLICATION_ABSOLUTE_URL ?>playlist.php');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	
	// hideLoadingPopup();
}


function popupPlaylist() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: '<?= $pathToPhpRoot ?>popupPlaylist.php',
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#popup_playlist').html(data);
			openEmptyPlaylistDialog();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}


function createPlaylist() {
	var nom_playlist = $('#createPlaylistName').val().trim();
	if (nom_playlist == "") {
		alert('Saisissez un nom de playlist');
		return false;
	}

	showLoadingPopup();
	
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/managePlaylistController.php',
		dataType : 'json',
		async : false,
		data: {
			action : 'addPlaylist',
			nom_playlist : nom_playlist
		},
		success: function(data, textStatus, jqXHR) {
			window.location.replace('<?= APPLICATION_ABSOLUTE_URL ?>playlist.php');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	
	// hideLoadingPopup();
}



function showFolder(id_folder, mode, aButton) {
	showLoadingPopup();
	
	$.ajax({
		type: 'POST', 
		url: 'playlist_panel_ajaxCall.php',
		dataType : 'html',
		async : false,
		data: {
			id_folder : id_folder,
			mode : mode
		},
		success: function(data, textStatus, jqXHR) {
			var div = $(aButton).parents('.playlist_list_content');
			$(div).find('.folder_detail ul li:not(.folder_up)').remove();
			$(div).find('.folder_detail ul').append(jqXHR.responseText);

			$(div).find('.playlists').toggle("slide", { direction : "left" } , 500, function() {
				$(div).find('.folder_detail').show("slide", { direction : "up" } , 500);
			});
			activateDragDrop();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	hideLoadingPopup();
}


function editFolder(id_folder) {
	showLoadingPopup();
	
	$.ajax({
		type: 'POST', 
		url: 'playlist_edit_folder.php', 
		dataType : 'html',
		async : false,
		data: {
			id : id_folder
		},
		success: function(data, textStatus, jqXHR) {
			$('#contenu_playlist').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	hideLoadingPopup();
}


function hideFolder(mode, aButton) {
	showLoadingPopup();
	
	$.ajax({
		type: 'POST', 
		url: 'playlist_panel_ajaxCall.php',
		dataType : 'html',
		async : false,
		data: {
			id_folder : null,
			mode : mode
		},
		success: function(data, textStatus, jqXHR) {
			var div = $(aButton).parents('.playlist_list_content');
			$(div).find('.playlists ul li').remove();
			$(div).find('.playlists ul').append(jqXHR.responseText);
			
			$(div).find('.folder_detail').toggle("slide", { direction : "up" } , 500, function() {
				$(div).find('.playlists').show("slide", { direction : "left" } , 500);
			});
			activateDragDrop();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	hideLoadingPopup();
	
}

var isDragging = false;
function activateDragDrop() {
	$( ".draggable_item" ).draggable({ 
		revert: "invalid",
		start: function() {
			isDragging = true;
		}, 
		stop: function() {
			isDragging = false;
		},
	});
    $( ".droppable_folder" ).droppable({
    	hoverClass: "ui-state-active",
		drop: function( event, ui ) {
			// $(this).remove();
			var id_folder = $(this).attr('folder_id');
			var id_playlist = $(ui.draggable).attr('playlist_id');

			showLoadingPopup();
			
			$.ajax({
				type: 'POST', 
				url: 'ajaxController/managePlaylistController.php',
				dataType : 'json',
				async : false,
				data: {
					action : 'addPlaylistInFolder',
					id_folder : id_folder,
					id_playlist : id_playlist
				},
				success: function(data, textStatus, jqXHR) {
					$(ui.draggable).remove();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert("Une erreur est survenue : \n" + jqXHR.responseText);
				}
			});
			
			hideLoadingPopup();
		}
    });
}





$(document).ready(function() {
	$('#addFolder').button({
		icons: {
			primary: "ui-icon-folder-open"
		}
	}).click(function() {
		$('#createFolder').dialog("open");
	});
	
	$('#createFolderButton').button({
		icons: {
			primary: "ui-icon-circle-plus"
		}
	}).click(function() {
		createPlaylistFolder();
	});

	$('#addPlaylistButton').button({
		icons: {
			primary: "ui-icon-video"
		}
	}).click(function() {
		$('#createPlaylist').dialog("open");
	});

	$('#createPlaylistButton').button({
		icons: {
			primary: "ui-icon-circle-plus"
		}
	}).click(function() {
		createPlaylist();
	});
	
	

	$('#createFolder').dialog({
		autoOpen: false,
		modal: true,
		width : 400,
		height : 130
	});
	
	$('#createPlaylist').dialog({
		autoOpen: false,
		modal: true,
		width : 400,
		height : 130
	});

	$('.dialog_input').keypress(function(event) {
		if (event.keyCode == 13) {
			$(this).find('button').click();
		}
	});


	// Problème de scroll qui augmente à l'infini : http://jsfiddle.net/crowjonah/Fr7u8/3/
	activateDragDrop();
});


</script>




<?php
include_once $pathToPhpRoot."pied.php";
?>
