<?php
session_start();

$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$id_playlist = $_POST['id'];

$playlistDTO = MetierPlaylist::getPlaylistWithVideo(CONNECTED_USER_ID, $id_playlist);

?>

<div class="bouton_retour">
	<img src="style/images/fleche_gauche.png" />
	<a href="playlist.php">Retour aux playlists</a>
</div>

<div id="playlist_lecteur">
	<div id="playlist_lecteur_titre">
		<?= $playlistDTO->playlist->nom ?>
	</div>
	<div id="playlist_lecteur_video">
		<div id="player">Chargement de la vidéo...</div>
		<!--  <video id="playlist_player" width="640" height="360" controls></video>
		
		<div id="player_srt" class="srt"
			data-video="playlist_player"
			data-srt="<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>testSub.srt"
			style="display : none;">
			
			<div class="text"></div>
			
			<a id="player_srt_close" href="#" onClick="$('#player_srt').hide(); $(this).hide(); return false;">
				<img src="style/images/close_srt.png" />
			</a>
		</div>
		-->
		
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
					<?php if ($evenement != null) { ?>
					<span class="video_event" title='<?= escapeSimpleQuoteHTML($evenement->nom) ?>'><?= $evenement->nom ?></span>
					<?php } ?>
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
	var array_video = {};
	var array_length = 0;
	<?php foreach ($playlistDTO->videos as $videoDTO) {
		$video = $videoDTO->video; 
	?>
	array_video[array_length] = <?= $video->id ?>;
	array_length++;
	<?php } ?>

	var idx_video_played = 0;

	function launchVideo(p_idx_video_played) {
		console.log("launch video : " + p_idx_video_played);
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=getVideoWithPasses&id=" + p_idx_video_played
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var videoDto = data.infos['video'];
					
					// $('.playlist_lecteur_titre_video').html(videoDto.video.nom_affiche);
					// $('#playlist_player')[0].src = '<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video;
					// $('#shareVideo input').val('<?= APPLICATION_ABSOLUTE_URL ?>showVideo.php?id=' + videoDto.video.code_partage);
					jwplayer("player").setup({
						file: encodeURI('<?= APPLICATION_ABSOLUTE_URL.PATH_CONVERTED_FILE."/" ?>' + videoDto.video.nom_video),
						image: encodeURI('<?= APPLICATION_ABSOLUTE_URL.PATH_CONVERTED_FILE."/" ?>' + videoDto.video.nom_video +'.jpg'),
						width : '',
						height : '100%',
						tracks: [{ 
							file: encodeURI(escapeSpaces('<?= APPLICATION_ABSOLUTE_URL.PATH_CONVERTED_FILE."/" ?>' + videoDto.video.nom_video + '.vtt')), 
							label: "French",
							kind: "captions",
							"default": true 
						}],
						skin: {
							name: "lecteur",
						},
					    captions: {
					        color: '#FFFFFF',
					        fontFamily : 'Tahoma, Arial, serif',
					        backgroundOpacity: 50,
					        edgeStyle : 'dropshadow'
					    }
					});
					jwplayer("player").on("complete", function() {
						if ($('#read_continu').is(':checked')) {
							playNext();
						}
				    });
					var statusBeforeSeek = null;
					jwplayer("player").on('seek', function() {
						statusBeforeSeek = jwplayer("player").getState();
					});
					jwplayer("player").on('seeked', function() {
						if (statusBeforeSeek == 'paused') {
							jwplayer("player").pause(true);
						}
						statusBeforeSeek = null;
					});
					
					
	
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
					
					// $('#player_srt').attr('data-srt', escapeSpaces('<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video + ".srt"));
					// $('#player_srt').attr('data-srt', escapeSpaces('<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>1183_Big Apple - Sequence 15.mp4.webm.srt'));
					// launchSubtitles();
					// $('#playlist_player video')[0].play();
					// idx_video_played = p_idx_video_played;
					hideLoadingPopup();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
				hideLoadingPopup();
			}
		});
	}

	function playNext() {
		idx_video_played++;
		console.log("play next array_length (" + array_length + ") > idx_video_played (" + idx_video_played + ") => " + (array_length > idx_video_played));
		if (array_length > idx_video_played) {
			launchVideo(array_video[idx_video_played]);
		}
	}
	
	function playerGoto(time) {
		var t = toSeconds(time);
		// document.getElementById("playlist_player").currentTime = $t;
		jwplayer("player").seek(t);
		// Coupe et remet les sous-titres pour les recharger (sinon bug en cas de relecture de la vidéo)
		jwplayer("player").setCurrentCaptions(0);
		jwplayer("player").setCurrentCaptions(1);
	}
	
	$(document).ready(function() {
		if (array_length == 0) {
			alert('Playlist vide');
			return;
		}
		
		if (array_length > idx_video_played) {
			launchVideo(array_video[idx_video_played]);
		} else {
			alert('Indice de vidéo inconnu : ' + idx_video_played);
		}

	});

</script>

