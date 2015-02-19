<?php
$pathToPhpRoot = './';

include_once $pathToPhpRoot."entete.php";

$allDansesNiveaux = MetierVideo::getAllVideosWithAttributesForDanseNiveau();

?>

<div id="title">
	<h1>Lister par danses et par niveau</h1>
</div>

<div id="danses" class="listeDiv">
	 <ul>
	 	<?php 
		foreach ($allDansesNiveaux as $nom_danse => $arrayNiveaux) {
		?>
		<li><a href="#tabs-<?= formatId($nom_danse) ?>" onClick="$('.action_check').attr('checked', false);"><?= $nom_danse ?></a></li>
		<?php
		} 
		?>
	</ul>
	
	<?php 
	include $pathToPhpRoot."liste_actionSelect.php";
	?>

	<?php 
	foreach ($allDansesNiveaux as $nom_danse => $arrayNiveaux) {
	?>
		<div id="tabs-<?= formatId($nom_danse) ?>" class="niveaux categories">
		<?php 
		foreach ($arrayNiveaux as $niveau) {
			$label = $NIVEAUX[$niveau['nom_niveau']];
			$nbVideos = $niveau['cpt'];
			if ($nbVideos < 2) {
				$nbVideosLabel = " ($nbVideos vidéo)";
			} else {
				$nbVideosLabel = " ($nbVideos vidéos)";
			}
		?>
			<h3 id="h3_<?= $niveau['id_danse'] ?>_<?= $niveau['nom_niveau'] ?>" 
				<?php if ($nbVideos > 0) { ?> onClick="getVideos(<?= $niveau['id_danse'] ?>, '<?= $niveau['nom_niveau'] ?>');" <?php } ?>
				>
				<?= $label.$nbVideosLabel ?>
			</h3>
			<div id="div_<?= $niveau['id_danse'] ?>_<?= $niveau['nom_niveau'] ?>" >
				<?php 
				if ($nbVideos == 0) {
					echo "<h2>Pas de vidéo</h2>";
				} else {
				?>
				
				<i>&nbsp;&nbsp;&nbsp;&nbsp;Recherche des vidéos...</i> <img src="style/images/loading.gif" />
				
				<?php } // end else  ?>
			</div>
			<?php } // end foreach niveau ?>	
		</div>
	<?php } // end foreach danses ?>
</div>

<script type="text/javascript">
	
	$(document).ready(function() {
		
		$( ".niveaux" ).accordion({ 
			active: false,
			collapsible : true,
			heightStyle: "content" 
		});

		/*
		$('.videoNiveau').dataTable( {
			"bJQueryUI": true,
			"iDisplayLength": -1,
			"aoColumns": [
				{ "bSortable": false },
	  			null,
	  			null,
	  			null,
	  			null,
	  			null,
	  			{ "bSortable": false }
			],
			"aaSorting": [[ 1, "asc" ]]
		});
		*/

		$('#danses').tabs();

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


	var niveauRetrieved = new Array();
	
	function getVideos(id_danse, id_niveau) {
		if ($.inArray(id_danse + "_" + id_niveau, niveauRetrieved) < 0) {
			$('#h3_' + id_danse + "_" + id_niveau).html(
					$('#h3_' + id_danse + "_" + id_niveau).html() +
					' <img src="style/images/loading.gif" id="loading_' + id_danse + "_" + id_niveau + '" style="height : 13px"/>'
			);
			
			$.ajax({
				type: 'POST', // Le type de ma requete
				url: 'ajaxController/getVideoByNiveau.php', // L'url vers laquelle la requete sera envoyee
				dataType : 'html',
				data: {
					danse : id_danse,
					niveau : id_niveau
				},
				async : false,
				success: function(data, textStatus, jqXHR) {
					$('.action_check').attr('checked', false);
					$('#div_' + id_danse + "_" + id_niveau).html(data);
					$('#loading_' + id_danse + "_" + id_niveau).remove();
					niveauRetrieved.push(id_danse + "_" + id_niveau);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert("Une erreur est survenue : \n" + jqXHR.responseText);
				}
			});
		} else {
			$('.action_check').attr('checked', false);
		}
	}
	
</script>

<?php
include $pathToPhpRoot."playerDialog.php";

include_once $pathToPhpRoot."pied.php";
?>