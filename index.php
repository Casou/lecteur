<?php
$pathToPhpRoot = './';
include_once "entete.php";

$statsVideos = MetierStat::getNbVideosByDanseAndType();
$dansesActivated = MetierDanse::getDanseActivatedByUser($_SESSION["userId"]);

// ffmpeg -i intro.mp4 -f webm -vcodec libvpx -acodec libvorbis -ab 160000 -crf 22 test.webm

$idDanses = array();
$nomDanses = array();
$statDanses = array();
foreach($statsVideos as $nomDanse => $stat) {
	$nomDanses[] = $nomDanse;
	$statDanses[] = $stat;
	$idDanses[] = $stat->idDanse;
	$switchOn = false;
	foreach($dansesActivated as $danseActivated) {
		if ($stat->idDanse == $danseActivated->id) {
			$switchOn = true;			
		}
	}
	$switchDanses[] = $switchOn;
}

$stats = MetierStat::getStatsVideo();

$NB_COLONNES = 4;

$news = MetierNews::getAvailableNews();

?>

<?php if ($isPhone) { ?>
<div class="ui-widget">
	<a href="mobile/" class="mobile-version">
		Retourner à la version mobile
	</a>
</div>
<?php } else if (!Fwk::isUsingFirefox()) { 
	$infoNavigateur = Fwk::getNavigateur(); 
?>
<div id="infoDiv" class="ui-widget no-border">
	<div id="error_login" class="ui-state-error ui-corner-all">
		Ce site est optimisé pour <strong>Firefox</strong> (Chrome marche pas mal aussi mais il peut subsiter quelques erreurs). En utilisant un autre navigateur, 
		il se peut que certaines fonctionalités ne fonctionnent pas.<br/>
		Vous utilisez actuellement : <?= $infoNavigateur[1] ?> 
	</div>
</div>
<?php } ?>


<?php if (count($news) > 0) { ?>
<div id="news" class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		News
	</div>
	
	<div class="ui-widget-content">
		<?php foreach($news as $news_record) { ?>
		<hr />
		<div class="news_record"><?= $news_record->texte ?></div>
		<?php } ?>
	</div>
</div>
<?php } ?>

<div id="indexStat" class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Statistiques
	</div>
	<div id="indexStatDivBody" class="ui-widget-content">
		<h3 style="text-align : center; margin : 0; padding-top : 15px;">
			Nombre de vidéos : <?= $stats->nbVideos ?> |
			Durée totale : <?= $stats->dureeTotale ?> |
			Nombre de passes : <?= $stats->nbPasses ?>  
		</h3>
		<?php if (isset($_SESSION[DROIT_ADMIN])) { ?>
			<h4 id="thumbnails_stats">
				<span id="stats_thumbnails_texte">
				<?php if (file_exists(PATH_THUMBNAIL_FILE)) { 
						echo file_get_contents(PATH_THUMBNAIL_FILE); 
					} else { ?>
						Pas encore de vérification des thumbnails.
				<?php } ?>
				</span>
				<a href="#" onClick="majStatsThumbnail(); return false;"><img src="style/images/actualiser.png" class="refresh_icon" /></a>
			</h4>
		<?php } ?>
		
		
		<table id="indexStatTable">
			<thead>
			<tr>
				<th colspan="<?= $NB_COLONNES ?>">Nombre de vidéos par danse et par type</th>
			</tr>
			</thead>
			<tbody>
		<?php
		
		$currentIndex = 0;
		do {
			echo "<tr>";
			for($i = 0; $i < $NB_COLONNES; $i++) {
				if (!isset($switchDanses[$i + $currentIndex])) {
					echo "<th> </th>";
				} else {
					$id = $idDanses[$i + $currentIndex];
					if ($switchDanses[$i + $currentIndex]) {
						echo '<th><div id="switch_'.$id.'" class="switch_on" '.
							'title="Activer / Désactiver l\'affichage de cette danse">'.
							'<a href="#" onClick="switch_on('.$id.', this); return false;" />'.
							'</th>';
					} else {
						echo '<th><div id="switch_'.$id.'" class="switch_off"'.
							'title="Activer / Désactiver l\'affichage de cette danse">'.
							'<a href="#" onClick="switch_on('.$id.', this); return false;" />'.
							'</th>';
					}
				}
			}
			echo "</tr>";

			echo "<tr>";
			for($i = 0; $i < $NB_COLONNES; $i++) {
				if (!isset($nomDanses[$i + $currentIndex])) {
					echo "<th> </th>";
				} else {
					$switchClass = "";
					if (!$switchDanses[$i + $currentIndex]) {
						$switchClass = "switched_off";
					}
					$id = $idDanses[$i + $currentIndex];
					echo "<th id=\"nom_$id\" class='stats_titre_danse $switchClass' onClick=\"$('.detail_stats').toggle();\" title='Afficher les détails'>".
						$nomDanses[$i + $currentIndex]."</th>";
				}
			}
			echo "</tr>";
			echo "<tr class='detail_stats'>";
			for($i = 0; $i < $NB_COLONNES; $i++) {
				if (!isset($statDanses[$i + $currentIndex])) {
					echo "<td> </td>";
				} else {
					$switchClass = "";
					if (!$switchDanses[$i + $currentIndex]) {
						$switchClass = "class='switched_off'";
					}
					
					$id = $idDanses[$i + $currentIndex];
					echo "<td id=\"stats_$id\" $switchClass>";
					$stat = $statDanses[$i + $currentIndex];
					foreach($VIDEO_TYPES as $videoType => $label) {
						if (isset($stat->arrayTypeNombre[$videoType])) {
							echo "<span style='float:left;'>$label : </span>".
								"<span style='float:right;'>".$stat->arrayTypeNombre[$videoType]."</span>";
						}
						echo "<br/>";
					}
					echo "</td>";
				}
			}
			echo "</tr>";
			
			$currentIndex += $NB_COLONNES;
		} while ($currentIndex < count($nomDanses));
		
		?>
			</tbody>
		</table>
	</div>
</div>

<script>
function switch_on(id, aTag) {
	showLoadingPopup();
	var div = $(aTag).parent();
	var switchOn = !div.hasClass('switch_on');
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'ajaxController/switchDanseController.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		data: {
			id_user : <?= $_SESSION["userId"] ?>,
			id_danse : id,
			switch_on : switchOn
		},
		success: function(data, textStatus, jqXHR) {
			if (!switchOn) {
				$('#switch_' + id).removeClass('switch_on');
				$('#switch_' + id).addClass('switch_off');
				$('#nom_' + id).addClass('switched_off');
				$('#stats_' + id).addClass('switched_off');
			} else {
				$('#switch_' + id).addClass('switch_on');
				$('#switch_' + id).removeClass('switch_off');
				$('#nom_' + id).removeClass('switched_off');
				$('#stats_' + id).removeClass('switched_off');
			}
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();	
		}
	});
}

function majStatsThumbnail() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'ajaxController/majStatsThumbnail.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		async : false,
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				$('#stats_thumbnails_texte').html(data.message);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}


$('.mobile-version').button({
	icons: {
		primary: "ui-icon-extlink"
	}
});
		

</script>


<?php
include_once "pied.php";
?>
	
