<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

// ******* /!\ Autoriser le short open tag /!\ *******


$runningEncodingVideos = MetierEncodageEnCours::getRunningEncodingVideos();
$freezeEncoding = false;
$encodingVideos = array();
foreach ($runningEncodingVideos as $video) {
	$encodingVideos[$video->nom_video] = $video;
	if ($video->bloquant && $video->etat != ENCODING_STATE_ENDED_OK) {
		$freezeEncoding = true;
	}
}

?>

<div id="title">
	<h1>Corbeille (vidéos déjà converties)</h1>
</div>

<button id="clearBin" style="margin-bottom : 20px;">Vider la corbeille</button>

<table id="manageVideoBinTable"  class="manageTable">
	<thead>
		<tr>
			<th class="nom">Nom</th>
			<th class="taille">Taille</th>
			<th class="action">Action</th>
		</tr>
	</thead>
	
	<tbody>

<?php
if ($handle = opendir($pathToPhpRoot.PATH_RAW_FILE_BIN)) {

	while (false !== ($entry = readdir($handle))) {
		$fileName = utf8_encode($entry);
		
		if($entry != "." && $entry != ".." && !endsWith($entry, ".log")) {
			$filesize = Fwk::getFormatedFileSize($pathToPhpRoot.PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$entry);
			/*
			// Taille en Ko
			$filesize = filesize(PATH_RAW_FILE_BIN.DIRECTORY_SEPARATOR.$entry) / 1000;
			$unite = "Ko";
			if ($filesize < 1000) {
				$filesize = round($filesize, 0);
			}
			// Taille en Mo
			if ($filesize > 1000) {
				$filesize = round($filesize / 1000, 2);
				$unite = "Mo";
			}
			// Taille en Go
			if ($filesize > 1000) {
				$filesize = round($filesize / 1000, 2);
				$unite = "Go";
			}
			$filesize .= " $unite";
			*/
?>
			<tr>
				<td class="nom"><?= $fileName ?></td>
				<td class="taille"><?= $filesize ?></td>
				<td class="action">
					<a href="#" onClick="moveFile('<?= escapeSimpleQuote($fileName) ?>', this); return false;" class="boutonAction">
						<img src="style/images/move.png" alt="Déplacer" 
							title="Remettre le fichier parmi les vidéos à convertir" />
					</a>
					<a href="#" onClick="deleteFile('<?= escapeSimpleQuote($fileName) ?>', this); return false;" class="boutonAction">
						<img src="style/images/delete.png" alt="Suppr" title="Supprimer le fichier" />
					</a>
				</td>
			</tr>
<?php 
		}
	}

	closedir($handle);
}

?>
	</tbody>
</table>

<script type="text/javascript">
	function moveFile(fileName, caller) {
		if (!confirm('Voulez-vous recharger cette vidéo dans les vidéos à convertir ?')) {
			return false;
		}
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=moveBinVideo&nom=" + encodeURI(fileName)
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					$(caller).parents('tr').hide(500, function() {
						$(caller).parents('tr').remove();
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}


	function deleteFile(fileName, caller) {
		if (!confirm('Voulez-vous supprimer définitivement cette vidéo ?')) {
			return false;
		}

		deleteFileAjax(fileName, caller);
	}

	function deleteFileAjax(fileName, caller) {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=deleteBinVideo&nom=" + encodeURI(fileName)
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					if (caller == null) {
						$('#manageVideoBinTable tbody tr').hide(500, function() {
							$('#manageVideoBinTable tbody tr').parents('tr').remove();
						});
					} else {
						$(caller).parents('tr').hide(500, function() {
							$(caller).parents('tr').remove();
						});
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	$(document).ready(function() {

		$("#clearBin").button({
			icons: {
				primary : "ui-icon-trash"
			}
		}).click(function() {
			if (confirm('Voulez-vous supprimer définitivement toutes les vidéos ?')) { 
				deleteFileAjax("all", null);
			}
		});

		$('#manageVideoBinTable').dataTable( {
			 "bJQueryUI": true,
			 "oLanguage": {
				"sLengthMenu": "Afficher _MENU_ enregistrements par page",
				"sZeroRecords": "Aucun enregistrement",
				"sInfo": "Enregistrement n°_START_ à _END_ / _TOTAL_",
				"sInfoEmpty": "Pas d'enregistrement à afficher",
				"sInfoFiltered": "(filtré sur _MAX_ enregistrements)"
			},
			"aoColumns": [
	  			  null,
	              { "sType": "capacite" },
	              { "bSortable": false }
	          ]
		});
	});

</script>


<?php
include_once $pathToPhpRoot."pied.php";
?>