<?php

session_start();

$pathToPhpRoot = "../";
include_once $pathToPhpRoot."includes.php";
$NB_VIDEOS_LIMIT = 50;

?>

<div class="bouton_retour">
	<img src="style/images/fleche_gauche.png" />
	<a href="#" onClick="modifyCriteria(); return false;">Modifier les critères</a>
</div>


<script type="text/javascript">
<!--

function modifyCriteria() {
	$('#div_recherche_resultat').hide();
	$('#div_recherche').show();
}


//-->
</script>



<?php 
$formulaire = $_POST['formulaire'];
$videos = MetierVideo::research($formulaire, $NB_VIDEOS_LIMIT);
?>

<?php if (count($videos) == $NB_VIDEOS_LIMIT) { ?>
<p class="research_limit">ATTENTION : Seuls les 50 premiers éléments sont affichés.</p>
<?php } ?>

<table id="table_resultat">
	<thead>
		<tr>
			<th class="nom_affiche">Nom - Evenement</th>
			<th class="type">Type</th>
			<th class="danses">Danses</th>
			<th class="passes">Passes</th>
			<th class="profs">Professeurs</th>
			<th class="pertinence" title="Pertinence">Pert.</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		foreach ($videos as $videoDTO) {
			$video = $videoDTO->video;
			$evenement = $videoDTO->evenement;
			$labelEvenement = "$evenement->date - $evenement->nom - $evenement->ville";
		?>
		<tr onClick='openPlayer(<?= $video->id ?>)' class="tr_video">
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
		</tr>
	<?php }?>
	</tbody>

</table>
