<?php
session_start();

$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";
Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$id_playlist = $_POST['id'];

$playlistDTO = MetierPlaylist::getPlaylistWithVideo($_SESSION['userId'], $id_playlist);

?>

<div id="playlist_lecteur">
	<div id="playlist_lecteur_titre">
		<?= $playlistDTO->playlist->nom ?>
	</div>
	<div id="playlist_lecteur_video">
		<video id="playlist_player" width="640" height="360" controls>
		</video>
		
		<input type="checkbox" id="read_continu" checked="checked" />
		<label for="read_continu">Lecture automatique</label>
		<ul>
			<?php foreach ($playlistDTO->videos as $videoDTO) {
				$video = $videoDTO->video;
				$evenement = $videoDTO->evenement; 
				
				$img = "style/images/thumbnail.jpg";
				if (file_exists("ressources/thumbnails/$video->nom_video.jpg")) {
					$img = "ressources/thumbnails/$video->nom_video.jpg";
				}
			?>
			<li id="playlist_video_<?= $video->id ?>" class="playlist_video_li">
				<a href="#" onClick="launchVideo(<?= $video->id ?>); return false;">
					<img src="<?= $img ?>" />
					<span class="video_title" title='<?= escapeSimpleQuoteHTML($video->nom_affiche) ?>'><?= $video->nom_affiche ?></span><br/>
					<span class="video_event" title='<?= escapeSimpleQuoteHTML($evenement->nom) ?>'><?= $evenement->nom ?></span>
				</a>
			</li>
			<?php } ?>
		</ul>
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

	var idx_video_played = 0;

	function launchVideo(id) {
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=getVideoWithPasses&id=" + id
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var videoDto = data.infos['video'];
					
					// $('.playlist_lecteur_titre_video').html(videoDto.video.nom_affiche);
					$('#playlist_player')[0].src = '<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video;
					/*
					$('#player_srt').attr('data-srt', escapeSpaces('<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video + ".srt"));
					$('#shareVideo input').val('<?= URL_APPLICATION ?>/showVideo.php?id=' + videoDto.video.code_partage);
					*/
	
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
					
					// launchSubtitles();
					$('#playlist_player').bind("ended", function() {
						if ($('#read_continu').is(':checked')) {
							playNext();
						}
				    });
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

