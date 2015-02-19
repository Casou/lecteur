<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

// ******* /!\ Autoriser le short open tag /!\ *******


if(isset($_GET['viewAll'])) {
	$allVideos = MetierVideo::getAllVideo();
	$isAllVideos = true;
} else {
	$allVideos = MetierVideo::getAllVideoEmpty();
	$isAllVideos = false;
}

?>

<div id="title">
	<h1>Vidéos converties</h1>
</div>

<div>
<?php if (!$isAllVideos) { ?>
	Seules les vidéos non classées sont affichées. <a href="manageVideos.php?viewAll">Afficher toutes les vidéos</a>
<?php } else { ?>
	Toutes les vidéos sont affichées.
<?php }?>
</div>

<table id="manageTreatedVideoTable" class="manageTable">
	<thead>
		<tr>
			<th class="id"></th>
			<th class="nom_affiche">Nom / Fichier</th>
			<th class="type">Type</th>
			<th class="evenement">Evènement</th>
			<th class="duree" title="Durée">D.</th>
			<th class="previsualiser">Actions</th>
		</tr>
	</thead>
	
	<tbody>

<?php
foreach ($allVideos as $video) {
	$nomEvenement = null;
	if ($video->id_evenement != null) {
		$evenement = MetierEvenement::getEvenementById($video->id_evenement);
		$nomEvenement = $evenement->nom;
	}
	
	$hasPasseTimed = MetierPasse::hasPasseTimed($video->id);
	
	$fileExists = true;
	$filesize = "??";
	if (file_exists($pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video)) {
		$filesize = Fwk::getFormatedFileSize($pathToPhpRoot.PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video);
		/*
		// Taille en Ko
		$filesize = @filesize(PATH_CONVERTED_FILE.DIRECTORY_SEPARATOR.$video->nom_video) / 1000;
		$unite = "";
		if ($filesize != 0) {
			$unite = "Ko";
			if ($filesize < 1000) {
				$filesize = round($filesize, 0);
			}
			// Taille en Mo
			if ($filesize > 1000) {
				$filesize = round($filesize / 1000, 2);
				$unite = "Mo";
			}
			// Taille en Go
			if ($filesize > 1000) {
				$filesize = round($filesize / 1000, 2);
				$unite = "Go";
			}
			$filesize .= " $unite";
		}
		*/
	} else {
		$fileExists = false;
	}
?>
			<tr id="video_<?= $video->id ?>">
				<td class="id">
					<?= $video->id ?>
				</td>
				<td class="nom_affiche">
					<input type="text" class="noBorder" 
						onFocus="$(this).removeClass('noBorder');"
						onBlur="$(this).addClass('noBorder');"
						onChange="changeNomAfficheVideo(<?= $video->id ?>, this);"
						value="<?= htmlspecialchars($video->nom_affiche) ?>"/>
						
					<span id="nom_affiche_hidden_<?= $video->id ?>" style="display : none;">
						<?= htmlspecialchars($video->nom_affiche) ?>
					</span>
						
					<?php if ($hasPasseTimed) { ?>
						<img src="style/images/timer.gif" 
							title="Les passes ont été timées." 
							style="width : 16px;" />
					<?php } ?>
					
					<br/>
					<span style="font-style : italic;">
						<?= $video->nom_video ?>
						(<?= $filesize ?>)
					</span>
				</td>
				<td class="type"><?= $video->type ?></td>
				<td class="evenement"><?= $nomEvenement ?></td>
				<td class="duree"><?= Fwk::formatDureeEnSecondes($video->duree) ?></td>
				<td class="action">
				<?php if ($fileExists) { ?>
					<!-- <a href="#" onClick="previsualize('<?= escapeSimpleQuote($video->nom_video) ?>'); return false;" -->
					<a href="#" onClick="previsualizeId(<?= escapeSimpleQuote($video->id) ?>); return false;" 
							action="convert"
							fileToConvert="<?= $video->nom_video ?>"
							name="video_<?= $video->id ?>">
						<img src="style/images/previsualisation.png" alt="Prévisu" alt="Prévisualiser" />
					</a>
					<a href="editVideoProperties.php?id=<?= $video->id ?>" target="_blank"> 
						<img src="style/images/modify.png" alt="Edit" alt="Editer les propriétés" />
					</a>
					<a href="#" onClick="deleteVideo(<?= $video->id ?>); return false;"> 
						<img src="style/images/delete.png" alt="Suppr" alt="Supprimer la vidéo" />
					</a>
				<?php } else { ?>
					<a href="#" onClick="return false;" name="video_<?= $video->id ?>">
						<img src="style/images/previsualisation_off.png" alt="Pas de prévisu" alt="Prévisualisation impossible (fichier inexistant)" />
					</a>
					<a href="#" onClick="return false;" > 
						<img src="style/images/modify_off.png" alt="Pas d'édit" alt="Edition des propriétés impossible (fichier inexistant)" />
					</a>
					<a href="#" onClick="deleteVideo(<?= $video->id ?>); return false;"> 
						<img src="style/images/delete.png" alt="Suppr" alt="Supprimer la vidéo" />
					</a>
				<?php } ?>
					
				</td>
			</tr>
<?php 
}
?>
	</tbody>
</table>


<script type="text/javascript">

	function changeNomAfficheVideo(idVideo, input) {
		var oldValue = $('#nom_affiche_hidden_' + idVideo).html();
		oldValue = oldValue.trim();
		var newValue = input.value;
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				formulaire : "action=changeNomAfficheVideo&id=" + idVideo + "&nom=" + encodeURIComponent(newValue)
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					$('#nom_affiche_hidden_' + idVideo).html(newValue);

					// On MAJ la cellule via la méthode datatable pour MAJ l'index de recherche
					var htmlString = $(input).parents('.nom_affiche').html();
					htmlString = htmlString.replace(oldValue, newValue);
					
					var aPos = datatable.fnGetPosition($('#video_' + idVideo + ' .nom_affiche')[0]);
					datatable.fnUpdate(htmlString, aPos[0], aPos[1]);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function deleteVideo(idVideo) {
		if (!confirm('Voulez-vous supprimer cette vidéo ?')) {
			return false;
		}

		showLoadingPopup();
		
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				formulaire : "action=deleteVideo&id=" + idVideo
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					$("#video_" + idVideo).hide(500, function() {
						$("#video_" + idVideo).remove();
					});
				}
				hideLoadingPopup();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
				hideLoadingPopup();
			}
		});
	}

	/*
	jQuery.fn.dataTableExt.oSort['capacite-asc']  = function(a,b) {
		var capaciteA = getCapaciteInKo(a);
		var capaciteB = getCapaciteInKo(b);
		
	    return capaciteA - capaciteB;
	};
	
	jQuery.fn.dataTableExt.oSort['capacite-desc']  = function(a,b) {
		var capaciteA = getCapaciteInKo(a);
		var capaciteB = getCapaciteInKo(b);
		
	    return capaciteB - capaciteA;
	};
	*/

	$.fn.dataTableExt.afnSortData['dom-text'] = function ( oSettings, iColumn ) {
		return $.map( oSettings.oApi._fnGetTrNodes(oSettings), function (tr, i) {
			return $('td:eq(' + iColumn + ') input', tr).val();
		} );
	}

	$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings ) {
	  return {
	    "iStart":         oSettings._iDisplayStart,
	    "iEnd":           oSettings.fnDisplayEnd(),
	    "iLength":        oSettings._iDisplayLength,
	    "iTotal":         oSettings.fnRecordsTotal(),
	    "iFilteredTotal": oSettings.fnRecordsDisplay(),
	    "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
	    "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	  };
	}
	

	var datatable = null;

	$(document).ready(function() {
		var displayLength = 20;
		
		datatable = $('#manageTreatedVideoTable').dataTable( {
			 "bJQueryUI": true,
			 "oLanguage": {
				"sLengthMenu": "Afficher _MENU_ enregistrements par page",
				"sZeroRecords": "Aucun enregistrement",
				"sInfo": "Enregistrement n°_START_ à _END_ / _TOTAL_",
				"sInfoEmpty": "Pas d'enregistrement à afficher",
				"sInfoFiltered": "(filtré sur _MAX_ enregistrements)"
			},
			"aLengthMenu": [
							 [20, 50, 100, -1],
							 [20, 50, 100, "Tous"]
						],
			"iDisplayLength": displayLength,
			"sPaginationType": "full_numbers",
			"aoColumns": [
				null,
				{ "sSortDataType": "dom-text" },
	            null,
	            null,
	            null,
	            { "bSortable": false }
	          ],
	        "aaSorting": [[ 0, "desc" ]]
		});


		/*
		// On pagine à la bonne page pour la vidéo ancrée (#video_???)
		var nbPages = datatable.fnPagingInfo().iTotalPages;

		// On fait un calcul approximatif de la page où devrait se trouver la vidéo
		var displayStart = 0;
		var idVideo = window.location.hash;
		var videoAnchor = "";
	    if (idVideo != undefined && idVideo != "") {
	    	idVideo = idVideo.substring(1, idVideo.length); // enleve le #
		    videoAnchor = idVideo;
		    idVideo = idVideo.substring(6, idVideo.length); // enleve le "video_"
		    displayStart = nbPages - Math.round(idVideo / displayLength) - 1;

		    if (displayStart < 0) {
		    	displayStart = 0;
		    }
	    } else {
	    	idVideo = "NaN";
	    }

		datatable.fnPageChange( displayStart );

		// Si la vidéo ne se trouve pas à la page demandée, on essaie de la trouver 
		// dans les 3 prochaines pages 
		if ($('#' + videoAnchor).length == 0 && !isNaN(idVideo)) {
			var idTr = $('#manageTreatedVideoTable tbody tr').first().attr('id');
			var idFirstVideo = idTr.substring(6, idTr.length);
			var action = "next";
			if (idVideo > idFirstVideo) {
				action = "previous";
			}

			for (var i = 0; i < 3; i++) {
				datatable.fnPageChange(action);
				if ($('#' + videoAnchor).length != 0) {
					break;
				}
			}
		}
		*/

	});
	
</script>


<?php
include $pathToPhpRoot."playerDialog.php";

include_once $pathToPhpRoot."pied.php";
?>