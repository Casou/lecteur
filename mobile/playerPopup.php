<?php 
$pathToPhpRoot = "../";
include_once $pathToPhpRoot."includes.php";

?>
<div id="playerDialog" data-role="popup" title="Visualisation" style="text-align : center">

	<div data-role="header">
    	<h1>Visualisation</h1>
	</div>
	
	<div data-role="content">
		<h1 id="video_title"></h1>
	
    	<video id="player" title="Prévisualisation" width="90%" controls>
    		<source src=""></source>
		</video>
		
		<div id="playerPasses">
			<table>
			</table>
		</div>
		
		<script type="text/javascript">
			function playerGoto(time) {
				$t = toSeconds(time);
				document.getElementById("player").currentTime = $t;
			}
			function toSeconds(t) {
			    var s = 0.0;
			    if(t) {
			      var p = t.split(':');
			      for(i=0;i<p.length;i++)
			        s = s * 60 + parseFloat(p[i].replace(',', '.'))
			    }
			    return s;
			}

			var niveauLibelle = {
			<?php foreach($NIVEAUX as $niveau => $libelle) { ?>
				'<?= $niveau ?>' : '<?= $libelle ?>',
			<?php } ?>
			};
			
			function openPlayer(id) {
				showLoadingPopup();
				$.ajax({
					type: 'POST', 
					url: 'ajaxController/getVideoById.php',
					dataType : 'json',
					async : false,
					data: {
						id : id
					},
					success: function(data, textStatus, jqXHR) {
						if (data.status != 'OK') {
							alert('[' + data.status + '] ' + data.message);
						} else {
							var videoDto = data.infos['video'];
							
							$('#video_title').html(videoDto.video.nom_affiche);
							$('#player')[0].src = '../<?= changeBackToSlash(PATH_CONVERTED_FILE)."/" ?>' + videoDto.video.nom_video;
			
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
			
							$( "#playerDialog" ).popup( "open" );
							$('#player')[0].play();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert("Une erreur est survenue : \n" + jqXHR.responseText);
					}
				});
				hideLoadingPopup();
			}
		</script>
		
	</div>
</div>
