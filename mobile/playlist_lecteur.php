<?php
$title = "Playlists";
include_once "entete.php";

$id_playlist = $_GET['id'];
$playlistDTO = MetierPlaylist::getPlaylistWithVideo(CONNECTED_USER_ID, $id_playlist);

?>

<div class="bouton_retour">
	<img src="style/images/fleche_gauche.png" />
	<a href="playlist.php">Retour aux playlists</a>
</div>

<div id="div_playlist">
	<div id="playlist_lecteur_titre" class="ui-li-divider">
		<?= $playlistDTO->playlist->nom ?>
	</div>
	<div id="div_playlist_lecteur">
		<video id="playlist_player" width="640" height="360" controls>
		</video>
	</div>
	<div id="div_playlist_lecteur_menu_min_div">
		<div id="div_playlist_lecteur_menu_min">
			<a href="#" onClick="toggleMenu(); return false;">
				<img src="style/images/playlist.png" />
			</a>
		</div>
		<div id="div_playlist_lecteur_menu">
			<ul>
				<?php foreach ($playlistDTO->videos as $videoDTO) {
					$video = $videoDTO->video;
					$evenement = $videoDTO->evenement; 
					
					$img = "style/images/thumbnail.jpg";
					if (file_exists($pathToPhpRoot."ressources/thumbnails/$video->nom_video.jpg")) {
						$img = $pathToPhpRoot."ressources/thumbnails/$video->nom_video.jpg";
					}
				?>
				<li id="playlist_video_<?= $video->id ?>" class="playlist_video_li" title="<?= escapeDoubleQuote($video->nom_affiche) ?>">
					<a href="#" onClick="launchVideo(<?= $video->id ?>); toggleMenu(); return false;">
						<img src="<?= $img ?>" />
						<span class="video_title"><?= $video->nom_affiche ?></span><br/>
						<span class="video_event"><?= $evenement->nom ?></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	
	<div class="clear"></div>
	
	<div id="playlist_lecteur_passes">
		<table>
		</table>
	</div>
</div>

<script>
	var array_video = new Array();
	<?php foreach ($playlistDTO->videos as $videoDTO) {
		$video = $videoDTO->video; 
	?>
	array_video.push(<?= $video->id ?>);
	<?php } ?>

	var niveauLibelle = {
	<?php foreach($NIVEAUX as $niveau => $libelle) { ?>
		'<?= $niveau ?>' : '<?= $libelle ?>',
	<?php } ?>
	};
	
	var idx_video_played = 0;


	function toggleMenu() {
		if ($('#div_playlist_lecteur_menu_min a').hasClass('selected')) {
			$('#div_playlist_lecteur_menu').hide("blind", 500);
			$('#div_playlist_lecteur_menu_min a').removeClass('selected');
		} else {
			$('#div_playlist_lecteur_menu').show("blind", 500);
			$('#div_playlist_lecteur_menu_min a').addClass('selected');
		}
	}

	function launchVideo(id) {
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/getVideoById.php',
			dataType : 'json',
			data: {
				id : id
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var videoDto = data.infos['video'];
					
					// $('.playlist_lecteur_titre_video').html(videoDto.video.nom_affiche);
					$('#playlist_player')[0].src = '../<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video;
	
					$('#playlist_lecteur_passes table').html('');
					for (var i = 0; i < videoDto.passes.length; i++) {
						var passe = videoDto.passes[i];
						var html = "<tr>";
						html += "<td>" + niveauLibelle[passe.niveau] + " - " + passe.nom + "</td>";
						html += "<td>";
						if (passe.timer_debut != null) {
							html += "<td>[" + 
							"<a class=\"playerGoto\" href=\"#\" onClick=\"playerGoto('" + passe.timer_debut + "'); return false;\">" + passe.timer_debut + "</a>" + 
							"-<a class=\"playerGoto\" href=\"#\" onClick=\"playerGoto('" + passe.timer_fin + "'); return false;\">" + passe.timer_fin + "</a>]</td>";
						}
						html += "</tr>";
						$('#playlist_lecteur_passes table').append(html);
					}
	
					$('.playlist_video_li').removeClass('selected');
					$('#playlist_video_' + videoDto.video.id).addClass('selected');
	
					$('#playlist_player')[0].play();
				}
				hideLoadingPopup();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
				hideLoadingPopup();
			}
		});
	}

	function playNext() {
		idx_video_played++;
		if (array_video.length > idx_video_played) {
			launchVideo(array_video[idx_video_played]);
		}
	}
	
	function playerGoto(time) {
		$t = toSeconds(time);
		document.getElementById("playlist_player").currentTime = $t;
	}

	
	$(document).ready(function() {
		
		if (array_video.length == 0) {
			alert('Playlist vide');
			return;
		}
		
		if (array_video.length > idx_video_played) {
			launchVideo(array_video[idx_video_played]);
		} else {
			alert('Indice de vidéo inconnu : ' + idx_video_played);
		}
	});
	
</script>

<?php
include_once "pied.php";
?>