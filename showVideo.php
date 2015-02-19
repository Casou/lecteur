<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

if (!isset($_GET['id'])) {
	echo '<h1 class="error">L\'URL est incorrecte.</h1>';
	return false;
}

if (!isset($_SESSION[DROIT_CONSULT_PARTAGE])) {
	echo '<h1 class="error">Vous n\'avez pas les droits suffisants pour visionner des vidéos.</h1>';
	return false;
}

$code_partage = $_GET['id'];

$video = MetierVideo::getVideoByCodePartage($code_partage, true);
$isFavori = MetierVideo::isFavori($video->id);
$dansesVideo = MetierDanse::getDanseByVideo($video->id);
$evenementVideo = MetierEvenement::getEvenementByVideo($video->id, true);
$passesVideo = MetierPasse::getPasseByVideo($video->id);
$professeursVideo = MetierProfesseur::getProfesseurByVideo($video->id);

if ($video == null) {
	echo '<h1 class="error">Cette vidéo n\'existe pas ou vous ne possédez pas les droits pour la visionner</h1>';
	return;
}

?>

<div id="title">
	<h1>Visualisation de la vidéo : <?= $video->nom_video ?></h1>
</div>

<div id="playerDiv" style="width : 100%; margin-top : 15px; text-align : center;">
	<video id="player" title="Prévisualisation" width="640" height="360" controls>
		<source src="<?= changeBackToSlash(PATH_CONVERTED_FILE)."/".escapeDoubleQuote($video->nom_video) ?>" type="video/webm" />
	</video>
</div>

<form action="#" onSubmit="return false;" id="videoForm">
	<input type="hidden" name="action" value="saveVideoProperties" />
	<input type="hidden" name="id" value="<?= $video->id ?>" id="id_video" />

	<table id="showVideoTable" style="margin : 10px auto auto auto;">
		<?php if (isset($_SESSION[DROIT_CAN_BOOKMARK])) { ?>
		<tr>
			<th colspan="2">
				<a href="#" onClick="changeFavori(<?= $video->id ?>); return false;">
				<?php if($isFavori) { ?>
					<img src="style/images/favori.png" title="Retirer des favoris"
						class="favori_<?= $video->id ?>" />
				<?php } else { ?>
					<img src="style/images/favori_off.png" title="Ajouter des favoris"
						class="favori_<?= $video->id ?>" />
				<?php } ?>
					Favori
				</a>
			</th>
		</tr>
		<?php } ?>
		<tr>
			<th>Nom : </th>
			<td><?= $video->nom_affiche ?></td>
		</tr>
		<tr>
			<th>Type : </th>
			<td id="radioTypeVideo"><?= $video->type != null ? $VIDEO_TYPES[$video->type] : "" ?></td>
		</tr>
		<tr>
			<th>Danse(s) : </th>
			<td id="editVideoPropertiesCheckDanse">
				<?php 
				foreach ($dansesVideo as $danse) { 
					echo $danse->nom."<br/>" ;
				} ?>
			</td>
		</tr>
		
		<tr>
			<th>Evènement(s) : </th>
			<td>
				<?php 
				if ($evenementVideo != null) {
					echo "$evenementVideo->date - $evenementVideo->nom - $evenementVideo->ville";
				}
				?>
			</td>
		</tr>
		
		<tr>
			<th>Professeur(s) : </th>
			<td>
				<?php foreach($professeursVideo as $prof) { ?>
					<?= $prof->nom ?>
					<br/>
				<?php } ?>
			</td>
		</tr>
		
		<tr>
			<th>Passes : </th>
			<td>
				<?php foreach($passesVideo as $passe) { ?>
					<div class="passe">
						<?= htmlspecialchars($passe->nom)." - ".htmlspecialchars($NIVEAUX[$passe->niveau]) ?>
						
						<?php if ($passe->timer_debut != null && $passe->timer_fin != null) { ?>
							[<a href="#" class="playerGoto" onClick="playerGoto('<?= $passe->timer_debut ?>'); return false;"><?= $passe->timer_debut ?></a> - 
							<a href="#" class="playerGoto" onClick="playerGoto('<?= $passe->timer_fin ?>'); return false;"><?= $passe->timer_fin ?></a>]
						<?php } ?>
					</div>
				<?php } ?>
			</td>
		</tr>
	
	</table>
	
</form>














<script type="text/javascript">

var duree_video_seconds = <?= $video->duree ?>;


function checkTimer(timer) {
	if (trim(timer) == "") {
		return true;
	}
	
	// regular expression to match required time format 
	re = /^\d{2}:\d{2}:\d{2}$/; 
	if(!timer.match(re)) { 
		return false;
	}

	return true;
}

function playerGoto(time) {
	$t = toSeconds(time);
	document.getElementById("player").currentTime = $t;
}

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

include_once $pathToPhpRoot."pied.php";
?>