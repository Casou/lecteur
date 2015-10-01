<?php

$title = "Liste évènements";
include 'entete.php';

if(!isset($_GET['id_danse'])) {
	echo "Pas d'identifiant !!!</div></div>";
	exit;
}

$id_danse = $_GET['id_danse'];

$allEvenements = MetierEvenement::getAllEvenementWithVideoCountByDanse($id_danse, CONNECTED_USER_ID);
?>

<div id="evenements" data-role="collapsible-set">
	<?php 
	foreach ($allEvenements as $evenementDTO) {
		$evenement = $evenementDTO->evenement;
		$label = $evenement->date." - ".$evenement->nom." - ".$evenement->ville;
			
		$nbVideos = $evenementDTO->nbVideos;
		if ($nbVideos < 2) {
			$nbVideosLabel = " ($nbVideos vidéo)";
		} else {
			$nbVideosLabel = " ($nbVideos vidéos)";
		}
	?>
		<div data-role="collapsible" data-collapsed="true">
			<h3 id="h3_<?= $id_danse."_".$evenement->id ?>" 
				<?php if ($nbVideos > 0) { ?> onClick="getVideos(<?= $id_danse.", ".$evenement->id ?>);"> <?php } ?>
				<?= $label.$nbVideosLabel ?>
			</h3>
			<div id="div_<?= $id_danse."_".$evenement->id ?>">
				<?php 
				if ($nbVideos == 0) {
					echo "<h2>Pas de vidéo</h2>";
				} else {
				?>
				
				<i>&nbsp;&nbsp;&nbsp;&nbsp;Recherche des vidéos...</i> <img src="../style/images/loading.gif" />
				
				<?php } ?>
				
			</div>
		</div>
	<?php } // end foreach evenements ?>
</div>


<script type="text/javascript">
	
	var evenementRetrieved = new Array();
	
	function getVideos(id_danse, id_evenement) {
		if ($.inArray(id_danse + "_" + id_evenement, evenementRetrieved) < 0) {
			$('#h3_' + id_danse + "_" + id_evenement).html(
					$('#h3_' + id_danse + "_" + id_evenement).html() +
					' <img src="../style/images/loading.gif" id="loading_' + id_danse + "_" + id_evenement + '" style="height : 13px"/>'
			);
			
			$.ajax({
				type: 'POST', // Le type de ma requete
				url: 'ajaxController/getVideoByEvenement.php', // L'url vers laquelle la requete sera envoyee
				dataType : 'html',
				data: {
					danse : id_danse,
					evenement : id_evenement
				},
				async : false,
				success: function(data, textStatus, jqXHR) {
					$('#div_' + id_danse + "_" + id_evenement).html(data);
					$('#loading_' + id_danse + "_" + id_evenement).remove();
					evenementRetrieved.push(id_danse + "_" + id_evenement);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert("Une erreur est survenue : \n" + jqXHR.responseText);
				}
			});
		}
	}
	
</script>

<?php
include "playerPopup.php";

include_once "pied.php";
?>