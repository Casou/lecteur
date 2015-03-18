<?php 
$randomId = generateRandom();
?>
<table class="videoEvenement listeResultats" id="<?= $randomId ?>">
	<thead>
		<tr>
			<th class="check"><input type="checkbox" class="action_check masterCheckbox" onClick="masterCheckbox('<?= $randomId ?>');" /></th>
			<th class="favori"> </th>
			<th class="thumbnail"> </th>
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
		?>
		<tr>
			<td class="check">
				<input type="checkbox" class="action_check check_video" value="<?= $video->id ?>" />
			</td>
			<td class="favori">
				<?php if (isset($_SESSION[DROIT_CAN_BOOKMARK])) { ?>
					<a href="#" onClick="changeFavori(<?= $video->id ?>); return false;">
					<?php if($videoDTO->isFavori) { ?>
						<img src="style/images/favori.png" title="Retirer des favoris"
							class="favori_<?= $video->id ?>" />
					<?php } else { ?>
						<img src="style/images/favori_off.png" title="Ajouter des favoris"
							class="favori_<?= $video->id ?>" />
					<?php } ?>
					</a>
				<?php }?>
			</td>
			<td class="thumbnail">
				<?php 
				$thumbnail = 'style/images/thumbnail.jpg';
				if (file_exists($pathToPhpRoot."ressources/thumbnails/$video->nom_video.jpg")) {
					$thumbnail = "ressources/thumbnails/$video->nom_video.jpg";
				}
				?>
				<img src="<?= $thumbnail ?>" />
			</td>
			<td class="nom_affiche">
				<?= $video->nom_affiche ?>
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
				if (isset($manageNiveau) && $niveau != $passe->niveau) {
					echo '<span class="niveau_passe_autre">';
				}
				echo '<span class="nom_passe">'.$passe->nom."</span> - " .
					'<span class="niveau_passe">'.$NIVEAUX[$passe->niveau]."</span><br/>" ;
				if (isset($manageNiveau) && $niveau != $passe->niveau) {
					echo '</span>';
				}
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
					if (file_exists("..".DIRECTORY_SEPARATOR.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video)) { 
				?>
				<a href="#" onClick="previsualizeId(<?= $video->id ?>); return false;" 
						action="convert"
						fileToConvert="<?= $video->nom_video ?>"
						name="video_<?= $video->id ?>">
					<img src="style/images/previsualisation.png" alt="Visualiser" title="Visualiser" />
				</a>
				<br/>
				<?php } else { ?>
				<a href="#" onClick="previsualizeId(<?= $video->id ?>); return false;" 
						action="convert"
						fileToConvert="<?= $video->nom_video ?>"
						name="video_<?= $video->id ?>">
				<img src="style/images/previsualisation_off.png" alt="Fichier introuvable" 
					title="Visualisation impossible : fichier introuvable" />
				</a>
				<br/>
				<?php }
				} ?>
				<?= Fwk::formatDureeEnSecondes($video->duree) ?>
				
			</td>
		</tr>
		<?php } // end foreach tbody ?>
	</tbody>
</table>


<script type="text/javascript">
	function masterCheckbox(id) {
		$('#' + id + ' .check_video').prop('checked', $('#' + id + ' .masterCheckbox').prop('checked')); 
	}
	
	$(document).ready(function() {
		$('#<?= $randomId ?>').dataTable( {
			"bJQueryUI": true,
			"oLanguage": {
				"sLengthMenu": "Afficher _MENU_ enregistrements par page",
				"sZeroRecords": "Aucun enregistrement",
				"sInfo": "Enregistrement n°_START_ à _END_ / _TOTAL_",
				"sInfoEmpty": "Pas d'enregistrement à afficher",
				"sInfoFiltered": "(filtré sur _MAX_ enregistrements)"
			},
			"iDisplayLength": <?= VIDEO_PAGINATION_DEFAULT ?>,
			"aLengthMenu": [
							 [<?= VIDEO_PAGINATION_NB ?>],
							 [<?= VIDEO_PAGINATION_STRING ?>]
						],
			"aoColumns": [
				{ "bSortable": false },
				{ "bSortable": false },
				{ "bSortable": false },
	  			null,
	  			null,
	  			null,
	  			null,
	  			null,
	  			{ "bSortable": false }
			],
			"aaSorting": [[ 3, "asc" ]]
		});

		<?php if (count($videos) >  VIDEO_PAGINATION_DEFAULT ) { ?>
			$('.categories .fg-toolbar').css('display', 'block');
			$('#manageRawVideo .fg-toolbar').css('display', 'block');
		<?php } ?>
	});

</script>