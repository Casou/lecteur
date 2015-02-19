<?php
$pathToPhpRoot = './';

include_once $pathToPhpRoot."entete.php";

$allDansesProfesseurs = MetierVideo::getAllVideosWithAttributesForDanseProfesseur();

?>

<div id="title">
	<h1>Lister par danses et par professeur</h1>
</div>

<div id="danses" class="listeDiv">
	 <ul>
	 	<?php 
		foreach ($allDansesProfesseurs as $nom_danse => $arrayProfesseurs) {
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
	foreach ($allDansesProfesseurs as $nom_danse => $arrayProfesseurs) {
	?>
		<div id="tabs-<?= formatId($nom_danse) ?>" class="professeurs categories">
		<?php 
		foreach ($arrayProfesseurs as $professeur) {
			$nbVideos = $professeur["cpt"];
			if ($nbVideos < 2) {
				$nbVideosLabel = " ($nbVideos vidéo)";
			} else {
				$nbVideosLabel = " ($nbVideos vidéos)";
			}
		?>
			<h3 id="h3_<?= $professeur['id_danse'] ?>_<?= $professeur["id_prof"] ?>" 
				<?php if ($nbVideos > 0) { ?> onClick="getVideos(<?= $professeur['id_danse'] ?>, <?= $professeur["id_prof"] ?>);" <?php } ?>
				>
				<?= $professeur["nom_prof"].$nbVideosLabel ?>
			</h3>
			<div id="div_<?= $professeur['id_danse'] ?>_<?= $professeur['id_prof'] ?>" >
				<?php 
				if ($nbVideos == 0) {
					echo "<h2>Pas de vidéo</h2>";
				} else {
				?>
				
				<i>&nbsp;&nbsp;&nbsp;&nbsp;Recherche des vidéos...</i> <img src="style/images/loading.gif" />
				
				<?php } // end else  ?>
			</div>
			<?php } // end foreach evenements ?>	
		</div>
	<?php } // end foreach danses ?>
</div>

<script type="text/javascript">

	$(document).ready(function() {
		
		$( ".professeurs" ).accordion({ 
			active: false,
			collapsible : true,
			heightStyle: "content" 
		});

		/*
		$('.videoProfesseur').dataTable( {
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

	var profRetrieved = new Array();
	function getVideos(id_danse, id_prof) {
		if ($.inArray(id_danse + "_" + id_prof, profRetrieved) < 0) {
			$('#h3_' + id_danse + "_" + id_prof).html(
					$('#h3_' + id_danse + "_" + id_prof).html() +
					' <img src="style/images/loading.gif" id="loading_' + id_danse + "_" + id_prof + '" style="height : 13px"/>'
			);
			
			$.ajax({
				type: 'POST', // Le type de ma requete
				url: 'ajaxController/getVideoByProf.php', // L'url vers laquelle la requete sera envoyee
				dataType : 'html',
				data: {
					danse : id_danse,
					prof : id_prof
				},
				async : false,
				success: function(data, textStatus, jqXHR) {
					$('#div_' + id_danse + "_" + id_prof).html(data);
					$('#loading_' + id_danse + "_" + id_prof).remove();
					profRetrieved.push(id_danse + "_" + id_prof);
					$('.action_check').attr('checked', false);
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