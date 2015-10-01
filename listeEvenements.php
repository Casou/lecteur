<?php
$pathToPhpRoot = './';

$includeSelect = true;
if (!isset($_POST['id_user_monitored'])) {
	include_once $pathToPhpRoot."entete.php";
	
	$id_user = CONNECTED_USER_ID;
	$allDansesEvenements = MetierEvenement::getAllEvenementWithVideoCount($id_user);
} else {
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	
	include_once $pathToPhpRoot."includes.php";
	Logger::init($pathToPhpRoot);

	$id_user = $_POST['id_user_monitored'];
	$allDansesEvenements = MetierEvenement::getAllEvenementWithVideoCount($id_user, true);
	
	$includeSelect = false;
}

$danseOrder = MetierDanse::getDansesOrderedByUserPreference($id_user);
$dansesName = MetierDanse::getAllDanseName(true);
?>

<div id="title">
	<h1>Lister par danses et par évènements</h1>
</div>

<div id="danses" class="listeDiv">
	 <ul>
	 	<?php 
	 	foreach($danseOrder as $danseOrd) {
	 		if (isset($allDansesEvenements[$danseOrd->id])) {
	 			$id_danse = $danseOrd->id;
		?>
		<li><a id="danse_<?= $id_danse ?>" href="#tabs-<?= $id_danse ?>" onClick="$('.action_check').attr('checked', false);"><?= $dansesName[$id_danse] ?></a></li>
		<?php
	 		}
		} 
		?>
	</ul>
	
	<?php 
	if ($includeSelect) {
		include $pathToPhpRoot."liste_actionSelect.php";
	}
	?>

	<?php 
	foreach ($allDansesEvenements as $id_danse => $evenements) {
	?>
		<div id="tabs-<?= $id_danse ?>" class="evenements categories">
				
		<?php 
		foreach ($evenements as $evenementDTO) {
			$evenement = $evenementDTO->evenement;
			$couleur = $evenementDTO->couleur;
			$danse = $evenementDTO->danse;
			
			$label = $evenement->date." - ".$evenement->nom." - ".$evenement->ville;
			
			$nbVideos = $evenementDTO->nbVideos;
			if ($nbVideos < 2) {
				$nbVideosLabel = " ($nbVideos vidéo)";
			} else {
				$nbVideosLabel = " ($nbVideos vidéos)";
			}
		?>
			<h3 id="h3_<?= $danse->id ?>_<?= $evenement->id ?>" class="<?= $couleur->css_class ?>" 
				<?php if ($nbVideos > 0) { ?> onClick="getVideos(<?= $danse->id ?>, <?= $evenement->id ?>);" <?php } ?>
				>
				<?= $label.$nbVideosLabel ?>
			</h3>
			<div id="div_<?= $danse->id ?>_<?= $evenement->id ?>" >
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
		$( ".evenements" ).accordion({ 
			active: false,
			collapsible : true,
			heightStyle: "content" 
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


	var evenementRetrieved = new Array();
	
	function getVideos(id_danse, id_evenement) {
		if ($.inArray(id_danse + "_" + id_evenement, evenementRetrieved) < 0) {
			$('#h3_' + id_danse + "_" + id_evenement).html(
					$('#h3_' + id_danse + "_" + id_evenement).html() +
					' <img src="style/images/loading.gif" id="loading_' + id_danse + "_" + id_evenement + '" style="height : 13px"/>'
			);
			
			$.ajax({
				type: 'POST', // Le type de ma requete
				url: 'ajaxController/getVideoByEvenement.php', // L'url vers laquelle la requete sera envoyee
				dataType : 'html',
				data: {
					danse : id_danse,
					evenement : id_evenement
					<?php if (isset($_POST['id_user_monitored'])) { ?>
					, id_user : <?= $_POST['id_user_monitored'] ?>
					<?php } ?>
				},
				async : false,
				success: function(data, textStatus, jqXHR) {
					$('#div_' + id_danse + "_" + id_evenement).html(data);
					$('#loading_' + id_danse + "_" + id_evenement).remove();
					evenementRetrieved.push(id_danse + "_" + id_evenement);
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