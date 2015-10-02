<?php

$pathToPhpRoot = './';

include_once $pathToPhpRoot."entete.php";

$allDansesFavori = MetierVideo::getAllVideosWithAttributesFavori();

$danseOrder = MetierDanse::getDansesOrderedByUserPreference(CONNECTED_USER_ID);
$dansesName = MetierDanse::getAllDanseName();

?>

<div id="title" class="liste_favoris">
	<h1>Liste des vidéos favorites par danse</h1>
</div>

<main id="liste_favoris">
	<div id="danses" class="listeDiv">
		 <ul>
		 	<?php 
		 	foreach($danseOrder as $danseOrd) {
		 		if (isset($allDansesFavori[$danseOrd->id])) {
		 			$id_danse = $danseOrd->id;
			?>
			<li><a id="danse_<?= $id_danse ?>" href="#tabs-<?= $id_danse ?>"><?= $dansesName[$id_danse] ?></a></li>
		 	<?php 	
		 		}
			} 
			?>
		</ul>
		
		<?php 
		include $pathToPhpRoot."liste_actionSelect.php";
		?>
	
		<?php 
		foreach ($allDansesFavori as $id_danse => $videos) {
		?>
			<div id="tabs-<?= $id_danse ?>" class="favoris categories">
			<?php 
			if (count($videos) == 0) {
				echo "<h2>Pas de vidéo</h2>";
			} else {
			?>
				<table id="favori_table" class="videoFavori listeResultats">
						<thead>
							<tr>
								<th class="check"><input type="checkbox" class="action_check masterCheckbox" onClick="masterCheckbox('favori_table');" /></th>
								<th class="favori"> </th>
								<th class="nom_affiche">Nom</th>
								<th class="type">Type</th>
								<th class="danses">Danses</th>
								<th class="passes">Passes</th>
								<th class="profs">Professeurs</th>
								<th class="actions previsualiser"> </th>
							</tr>
						</thead>
						<tbody>
						<?php 
						foreach ($videos as $videoDTO) {
							$video = $videoDTO->video;
							$evenement = $videoDTO->evenement;
							$labelEvenement = "$evenement->date - $evenement->nom - $evenement->ville";
						?>
							<tr>
								<td class="check">
									<input type="checkbox" class="action_check check_video" value="<?= $video->id ?>" />
								</td>
								<td class="favori">
									<a href="#" onClick="changeFavori(<?= $video->id ?>); return false;">
									<?php if($videoDTO->isFavori) { ?>
										<img src="style/images/favori.png" title="Retirer des favoris"
											class="favori_<?= $video->id ?>" />
									<?php } else { ?>
										<img src="style/images/favori_off.png" title="Ajouter des favoris"
											class="favori_<?= $video->id ?>" />
									<?php } ?>
									</a>
								</td>
								<td class="nom_affiche">
									<?= $video->nom_affiche ?>
									<br/>
									<span class="evenement"><?= $labelEvenement ?></span>
								</td>
								<td class="type"><?= $video->type ?></td>
								<td class="danses">
								<?php 
								foreach ($videoDTO->danses as $danse) { 
									echo $danse->nom."<br/>" ;
								} ?>
								</td>
								<td class="passes">
								<?php 
								foreach ($videoDTO->passes as $passe) { 
									echo '<span class="nom_passe">'.$passe->nom."</span> - " .
										'<span class="niveau_passe">'.$NIVEAUX[$passe->niveau]."</span><br/>" ;
								} ?>
								</td>
								<td class="profs">
								<?php 
								foreach ($videoDTO->professeurs as $professeur) { 
									echo $professeur->nom."<br/>" ;
								} ?>
								</td>
								<td class="actions previsualiser">
									<?php if (isset($_SESSION[DROIT_PLAY_VIDEO])) {
										if (file_exists($pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video)) { 
									?>
									<a href="#" onClick="previsualizeId(<?= $video->id ?>); return false;" 
											action="convert"
											fileToConvert="<?= $video->nom_video ?>"
											name="video_<?= $video->id ?>">
										<img src="style/images/previsualisation.png" alt="Visualiser" title="Visualiser" />
									</a>
									<br/>
									<?php } else { ?>
									<img src="style/images/previsualisation_off.png" alt="Fichier introuvable" 
										title="Visualisation impossible : fichier introuvable" />
									<br/>
									<?php }
									} ?>
									<?= Fwk::formatDureeEnSecondes($video->duree) ?>
									
								</td>
							</tr>
							<?php } // end foreach tbody ?>
						</tbody>
					</table>
					<?php } // end else ?>	
			</div>
		<?php } // end foreach danses ?>
	</div>
</main>

<script type="text/javascript">
function masterCheckbox(id) {
	$('#' + id + ' .check_video').prop('checked', $('#' + id + ' .masterCheckbox').prop('checked')); 
}
	
	$(document).ready(function() {

		$('.videoFavori').dataTable( {
			"bJQueryUI": true,
			"iDisplayLength": -1,
			"aoColumns": [
				{ "bSortable": false },
				{ "bSortable": false },
	  			null,
	  			null,
	  			null,
	  			null,
	  			null,
	  			{ "bSortable": false }
			],
			"aaSorting": [[ 2, "asc" ]]
		});

		$('#danses').tabs();
		$('#danses .ui-tabs-nav').sortable({
	        axis: "x",
	        update: function() {
				var csv = "";
				$("#danses > ul > li > a").each(function(i){
					csv += ( csv == "" ? "" : "," ) + this.id.substring(6);
				});
				$('#danses').tabs( "refresh" );
				$.ajax({
					type: 'POST', // Le type de ma requete
					url: 'ajaxController/manageController.php', // L'url vers laquelle la requete sera envoyee
					dataType : 'json',
					data: {
						formulaire : "action=updateTabOrder&ids=" + csv
					},
					async : true,
					success: function(data, textStatus, jqXHR) { },
					error: function(jqXHR, textStatus, errorThrown) {
						alert("Une erreur est survenue lors de la sauvegarde de l'ordre des onglets : \n" + jqXHR.responseText);
					}
				});
	        }
		});


		if ($(".action_select").length == 0) {
				$('.check').hide();
		}
	});


	function changeFavori(videoId) {
		var action = "addFavori";
		if(!$('.favori_' + videoId).attr('src').endsWith('_off.png')) {
			action = "removeFavori";
		}

		showLoadingPopup();
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				formulaire : "action=" + action + "&videoId=" + videoId
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				if (action == "addFavori") {
					$('.favori_' + videoId).attr('src', 'style/images/favori.png')
											.attr('title', 'Retirer des favoris');
				} else {
					$('.favori_' + videoId).attr('src', 'style/images/favori_off.png')
											.attr('title', 'Ajouter aux favoris');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
		hideLoadingPopup();
	}
	
</script>

<?php
include $pathToPhpRoot."playerDialog.php";

include_once $pathToPhpRoot."pied.php";
?>