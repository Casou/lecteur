<?php
$title = "Favoris";
include_once "entete.php";

$allDansesFavori = MetierVideo::getAllVideosWithAttributesFavori();
?>
<div id="div_favoris">
	<?php 
	foreach ($allDansesFavori as $nom_danse => $videos) {
	?>
	<table class="table_result">
		<thead>
			<tr>
				<th class="title" colspan="5"><?= $nom_danse ?></th>
			</tr>
		<?php if (count($videos) == 0) { ?>
		</thead>
		<tbody>
			<tr>
				<td colspan="5">Pas de vidéo</td>
			</tr>
		</tbody>
		<?php } else { ?>
		
			<tr>
				<th class="nom_affiche">Nom</th>
				<th class="type">Type</th>
				<th class="danses">Danses</th>
				<th class="passes">Passes</th>
				<th class="profs">Professeurs</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ($videos as $videoDTO) {
				$video = $videoDTO->video;
				$evenement = $videoDTO->evenement;
				$labelEvenement = "$evenement->date - $evenement->nom - $evenement->ville";
			?>
			<tr onClick='openDialog(<?= $video->id ?>)' class="tr_video">
				<td class="nom_affiche">
					<?= $video->nom_affiche ?>
					<br/>
					<span class="evenement"><?= $labelEvenement ?></span>
				</td>
				<td class="type"><?= $video->type ?></td>
				<td class="danses">
				<?php 
				foreach ($videoDTO->danses as $danse) { 
					echo "<div>$danse->nom</div>" ;
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
			</tr>
			<?php } // end foreach ?>
		<?php } // end if ?>
		</tbody>
	</table>
	
	<?php } // end foreach ?>

</div>

<script>
function openDialog(id) {
	$.mobile.changePage( "playerDialog.php?id=" + id, { role: "dialog" } );
}

</script>

<?php
include_once "pied.php";
?>