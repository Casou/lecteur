<?php

?>
<div id="playerDialog" title="Visualisation" style="text-align : center">
	<input type="hidden" id="video_viewed" />

	
	<!-- <video id="player" title="Prévisualisation" width="640" height="360" controls></video>
	
	<div id="player_srt" class="srt"
		data-video="player"
		data-srt="<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>testSub.srt"
		style="display : none">
		
		<div class="text"></div>
		
		<a id="player_srt_close" href="#" onClick="$('#player_srt').hide(); $(this).hide(); return false;">
			<img src="style/images/close_srt.png" />
		</a>
	</div>
	-->
	
	<div id="player">Chargement de la vidéo...</div>
	
	
	
	<div id="playerPasses">
		<table>
		</table>
	</div>
	
	
	<div id="shareVideo">
		<button onClick="shareVideo();" class="share">
		Partager
		</button>
		<input id="shareVideoKey" type="text" readonly="readonly" value="" onFocus="this.select();" />
		<?php if (isset($_SESSION[DROIT_EDIT_VIDEO])) { ?>
		<button onClick="window.open('editVideoProperties.php?id='+$('#video_viewed').val(), '_blank'); $('#player')[0].pause();" class="editProperties">
		Editer
		</button>
		<?php } ?>
	</div>
</div>

<script type="text/javascript">

	function playerGoto(time) {
		var t = toSeconds(time);
		// $("#player video")[0].currentTime = t;
		jwplayer("player").seek(t);
		// Coupe et remet les sous-titres pour les recharger (sinon bug en cas de relecture de la vidéo)
		jwplayer("player").setCurrentCaptions(0);
		jwplayer("player").setCurrentCaptions(1);
	}

	/*
	function previsualize(fileName) {
		$('#playerDialog').parent().children('.ui-dialog-titlebar').children('.ui-dialog-title').html(fileName);
		$('#player')[0].src = '<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + fileName;
		$('#player_srt').attr('data-srt', escapeSpaces('<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + fileName + ".srt"));
		launchSubtitles();
		$('#playerDialog').dialog("open");
		$('#player')[0].play();
		return false;
	}
	*/

	function shareVideo() {
		if ($('#shareVideoKey').is(':visible')) {
			$('#shareVideo button:not(.share)').show(); 
			$('#shareVideoKey').hide();
		} else {
			$('#shareVideo button:not(.share)').hide(); 
			$('#shareVideoKey').show().focus();
		}
	}

	function reinitActionButtons() {
		$('#shareVideo :not(button)').hide(); 
		$('#shareVideo button').show(); 
	}

	
	function previsualizeId(id) {
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

					// Titre de la popup
					$('#playerDialog').parent().children('.ui-dialog-titlebar').children('.ui-dialog-title').html(videoDto.video.nom_affiche);
					
					/*
					$('#player')[0].src = '<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video;
					$('#player_srt').attr('data-srt', escapeSpaces('<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video + ".srt"));
					*/
					jwplayer("player").setup({
						file: encodeURI('<?= APPLICATION_ABSOLUTE_URL.changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video),
						image: encodeURI('<?= APPLICATION_ABSOLUTE_URL.changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video + ".jpg"),
						width : '95%',
						height : '350px',
						tracks: [{ 
							file: encodeURI(escapeSpaces('<?= APPLICATION_ABSOLUTE_URL.changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video + ".vtt")), 
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

					$('#shareVideo input').val('<?= APPLICATION_ABSOLUTE_URL ?>showVideo.php?id=' + videoDto.video.code_partage);

					$('#playerPasses table').html('');
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
						$('#playerPasses table').append(html);
					}

					// launchSubtitles();
					$('#playerDialog').dialog("open");
					// jwplayer("player").play(true);
					// $('#player video')[0].play();

					$('#video_viewed').val(id);
					reinitActionButtons();
				}
				hideLoadingPopup();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
				hideLoadingPopup();
			}
		});
	}
	

	
	$(document).ready(function() {
		$('#playerDialog').dialog({
			dialogClass: "playerDialog_div",
			autoOpen: false,
			modal: true,
			width : 670,
			height : 635,
			close : function() {
				// $('#player')[0].pause();
				window.clearInterval(ival);
			}
		});

		/*
		$("#player").bind("ended", function() {
		    alert('Video ended!');
		});
		*/
	});
</script>