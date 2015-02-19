<?php 
$randomId = generateRandom();
?>
<table class="table_result" id="<?= $randomId ?>">
	<thead>
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
		?>
		<tr onClick='openDialog(<?= $video->id ?>)' class="tr_video">
			<td class="nom_affiche"><?= $video->nom_affiche ?>
			</td>
			<td class="type"><?= $video->type ?></td>
			<td class="danses">
				<div>
				<?php 
				foreach ($videoDTO->danses as $danse) { 
					echo $danse->nom."<br/>" ;
				} ?>
				</div>
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
				<div>
				<?php 
				foreach ($videoDTO->professeurs as $professeur) { 
					echo $professeur->nom."<br/>" ;
				} ?>
				</div>
			</td>
		</tr>
		<?php } // end foreach tbody ?>
	</tbody>
</table>


<script>
function openDialog(id) {
	$.mobile.changePage( "playerDialog.php?id=" + id, { role: "dialog" } );
}

</script>