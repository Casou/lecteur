<?php
session_start();
header('HTTP/1.1 200 OK');

$pathToPhpRoot = './';
include $pathToPhpRoot.'includes.php';
Logger::init($pathToPhpRoot);

$formulaire = $_POST['formulaire'];
$videos = MetierVideo::research($formulaire);
?>

<?php 
include $pathToPhpRoot."liste_actionSelect.php";
?>
	
<table id="table_resultat">
	<thead>
		<tr>
			<th class="check"><input type="checkbox" class="action_check masterCheckbox" onClick="masterCheckbox('table_resultat');" /></th>
			<th class="favori"> </th>
			<th class="thumbnail"> </th>
			<th class="nom_affiche">Nom - Evenement</th>
			<th class="type">Type</th>
			<th class="danses">Danses</th>
			<th class="passes">Passes</th>
			<th class="profs">Professeurs</th>
			<th class="pertinence" title="Pertinence">Pert.</th>
			<th class="actions previsualiser"> </th>
		</tr>
	</thead>
	<tbody>
	<?php 
		foreach ($videos as $videoDTO) {
			$video = $videoDTO->video;
			$evenement = $videoDTO->evenement;
			$labelEvenement = "";
			if ($evenement != null) {
				$labelEvenement = "$evenement->date - $evenement->nom - $evenement->ville";
			}
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
			<?php } ?>
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
				<br/>
				<br/>
				<span class="evenement"><?= $labelEvenement ?></span>
			</td>
			<td class="type"><?php if ($video->type != null) echo $VIDEO_TYPES[$video->type] ?></td>
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
			
			<td class="pertinence"><?= formatNumber($videoDTO->pertinence, 2) ?></td>
			
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
				<span style="font-style : italic;"><?= Fwk::formatDureeEnSecondes($video->duree) ?></span>
				
				<?php if (isset($_SESSION[DROIT_EDIT_VIDEO])) { ?>
				<a href="editVideoProperties.php?id=<?= $video->id ?>" target="_blank"> 
					<img src="style/images/modify.png" alt="Edit" alt="Editer les propriétés" />
				</a>
				<?php } ?>
			</td>
		</tr>
	<?php }?>
	</tbody>

</table>






<script>

$(document).ready(function() {

	/*
	jQuery.fn.dataTableExt.oSort['percent-asc']  = function(a,b) {
	    var x = (a == "-") ? 0 : a.replace( /%/, "" );
	    var y = (b == "-") ? 0 : b.replace( /%/, "" );
	    x = parseFloat( x.replace(",", ".") );
	    y = parseFloat( y.replace(",", ".") );
	    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};
	 
	jQuery.fn.dataTableExt.oSort['percent-desc'] = function(a,b) {
	    var x = (a == "-") ? 0 : a.replace( /%/, "" );
	    var y = (b == "-") ? 0 : b.replace( /%/, "" );
	    x = parseFloat( x.replace(",", ".") );
	    y = parseFloat( y.replace(",", ".") );
	    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};
	*/

	$('#table_resultat').dataTable( {
		 "bJQueryUI": true,
		 "oLanguage": {
			"sLengthMenu": "Afficher _MENU_ enregistrements par page",
			"sZeroRecords": "Aucun enregistrement",
			"sInfo": "Enregistrement n°_START_ à _END_ / _TOTAL_",
			"sInfoEmpty": "Pas d'enregistrement à afficher",
			"sInfoFiltered": "(filtré sur _MAX_ enregistrements)"
		},
		"aLengthMenu": [
						 [10, 20, 50, -1],
						 [10, 20, 50, "Tous"]
					],
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"aoColumns": [
			{ "bSortable": false },
		    { "bSortable": false },
		    { "bSortable": false },
			null,
 			null,
 			null,
			null,
			null,
			null,
			{ "bSortable": false }
		],
		"aaSorting": [[ 6, "desc" ]] // on trie par pertinance descendante
	});

	$('#table_resultat').ready(function() {
		$('#table_resultat th').removeAttr('style');
	});
	 
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
?>
