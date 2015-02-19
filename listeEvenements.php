<?php
$pathToPhpRoot = './';

$includeSelect = true;
if (!isset($_POST['id_user_monitored'])) {
	include_once $pathToPhpRoot."entete.php";
	
	$id_user = $_SESSION['userId'];
	$allDansesEvenements = MetierEvenement::getAllEvenementWithVideoCount($id_user);
} else {
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	
	include_once $pathToPhpRoot."includes.php";
	Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

	$id_user = $_POST['id_user_monitored'];
	$allDansesEvenements = MetierEvenement::getAllEvenementWithVideoCount($id_user, true);
	
	$includeSelect = false;
}

?>

<div id="title">
	<h1>Lister par danses et par évènements</h1>
</div>

<div id="danses" class="listeDiv">
	 <ul>
	 	<?php 
		foreach ($allDansesEvenements as $nom_danse => $evenements) {
		?>
		<li><a href="#tabs-<?= formatId($nom_danse) ?>" onClick="$('.action_check').attr('checked', false);"><?= $nom_danse ?></a></li>
		<?php
		} 
		?>
	</ul>
	
	<?php 
	if ($includeSelect) {
		include $pathToPhpRoot."liste_actionSelect.php";
	}
	?>

	<?php 
	foreach ($allDansesEvenements as $nom_danse => $evenements) {
	?>
		<div id="tabs-<?= formatId($nom_danse) ?>" class="evenements categories">
				
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