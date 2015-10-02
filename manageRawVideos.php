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

<div id="title" class="manage_raw_videos">
	<h1>Vidéos importées, non converties</h1>
</div>

<main id="manage_raw_videos">
	<div id="convertAllVideosDiv" style="float : right;">
		<button id="convertAllVideos">Convertir toutes les vidéos</button>
	</div>
	
	<div id="unlockVideosDiv">
		<button id="unlockVideos">Débloquer les vidéos</button>
	</div>
	
	<div id="manageRawVideo">
		<table id="manageRawVideoTable"  class="manageTable">
			<thead>
				<tr>
					<th class="nom">Nom</th>
					<th class="taille">Taille</th>
					<th class="action">Action</th>
				</tr>
			</thead>
			
			<tbody>
		
		<?php
		if ($handle = opendir($pathToPhpRoot.PATH_RAW_FILE)) {
		
			while (false !== ($entry = readdir($handle))) {
				// $fileName = iconv( "iso-8859-1", "utf-8", $entry );
				$fileName = utf8_encode($entry);
				
				if($entry != "." && $entry != ".." && !endsWith($entry, ".log")) {
					$filesize = Fwk::getFormatedFileSize($pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$entry);
					/*
					// Taille en Ko
					$filesize = filesize($pathToPhpRoot.PATH_RAW_FILE.DIRECTORY_SEPARATOR.$entry) / 1000;
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
							<a href="#" onClick="actionButton(this); return false;" 
									action="convert"
									fileToConvert="<?= $fileName ?>"
									class="boutonAction">
								<img src="style/images/convertir.gif" alt="Convertir" title="Convertir" />
							</a>
							<a href="#" class="logFile"></a>
							<a href="#" onClick="deleteFile(this); return false;"
								fileToDelete="<?= $fileName ?>"
								class="deleteIcon">
								<img src="style/images/delete.png" alt="Suppr" title="Supprimer le fichier" />
							</a>
							<span class="progress"></span>
							<span class="resting"></span>
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
	</div>
</main>


<div id="logDialog" title="Fichier de log">
	<h2 style="height : 7%; margin : 0;">Progression : <span id="logDialogTime"></span></h2>
	<div style="height : 92%;">
		<textarea id="logDialogContent"></textarea>
	</div>
</div>

<?php
include_once $pathToPhpRoot.'popupConvert.php'; 
?>

	<script type="text/javascript">


	var convertionLocked = false;

	// $(".action img").tooltip();
	
	
	function actionButton(caller) {
		action = $(caller).attr('action');
		if (action == "convert") {
			convert(caller);
		} else if (action == "complete") {
			complete(caller);
		} else {
			alert("[Méthode JS actionButton] Action non prise en charge : " + action);
		}
	}

	var lockDisabling = false;
	function disableConvertButtons() {
		if (!lockDisabling) {
			convertionLocked = true;
			
			$(".action img").each(function() {
				if ($(this).attr('src') == 'style/images/convertir.gif') {
					$(this).attr('src', 'style/images/convertir_bw.gif');
					$(this).attr('title', 'Un seul convertissement à la fois !!!');
				}
			});
	
			$("#unlockVideos").show();
		}
	}

	function enableConvertButtons() {
		if (!lockDisabling) {
			convertionLocked = false;
			
			$(".action img").each(function() {
				if ($(this).attr('src') == 'style/images/convertir_bw.gif') {
					$(this).attr('src', 'style/images/convertir.gif');
					$(this).attr('title', 'Convertir');
				}
			});
	
			$("#unlockVideos").hide();
		}
	}

	function convert(caller) {

		if ($(caller).children("img").attr('src') != "style/images/convertir.gif"
			&& $(caller).children("img").attr('src') != "style/images/error.png") {
			alert('Méthode convert : Pas la bonne icône');
			return false;
		}

		if (convertionLocked) {
			return false;
		}
		
		fileToConvert = $(caller).attr('fileToConvert');

		$('#fileToConvertHidden').val(fileToConvert);
		$('#convertDialog .fileName').html(fileToConvert);
		var format = getFileExtention(fileToConvert).toUpperCase();
		if ($('#convert' + format).size() == 0) {
			$('#convertDialog li').first().children('input').first().click();
		} else {
			$('#convert' + format).click();
		}

		$("#convertDialog").dialog('open');
		
	}

	function runningEncoding(fileToConvert) {
		if ($(".action a[fileToConvert='" + fileToConvert + "'] img").attr('src') == 'style/images/loading.gif') {
			return;
		}
		$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('src', 'style/images/loading.gif');
		$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('title', 'Encodage en cours');
		$(".action a[fileToConvert='" + fileToConvert + "']").attr('action', 'loading');
		addLogFileIcon(fileToConvert);
		hideDeleteIcon(fileToConvert);
	}

	function endingEncoding(fileToConvert) {
		$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('src', 'style/images/checked.png');
		$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('title', 'Encodage terminé. Cliquez pour rendre la vidéo disponible.');
		$(".action a[fileToConvert='" + fileToConvert + "']").attr('action', 'complete');
		addLogFileIcon(fileToConvert);
		hideDeleteIcon(fileToConvert);
	}

	function errorEncoding(fileToConvert, errorMessage) {
		$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('src', 'style/images/error.png');
		$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('title', 'Encodage en erreur : ' + errorMessage + 
				". Cliquez pour relancer le traitement.");
		$(".action a[fileToConvert='" + fileToConvert + "']").attr('action', 'convert');
		addLogFileIcon(fileToConvert);
		showDeleteIcon(fileToConvert);
	}

	function showDeleteIcon(fileToConvert) {
		$(".action a[fileToConvert='" + fileToConvert + "']").siblings(".deleteIcon").hide();
	}

	function hideDeleteIcon(fileToConvert) {
		$(".action a[fileToConvert='" + fileToConvert + "']").siblings(".deleteIcon").hide();
	}

	function addLogFileIcon(fileToConvert) {
		$(".action a[fileToConvert='" + fileToConvert + "']").siblings(".logFile").html(
				'<img src="style/images/log_file.gif" onClick="seeLogFile(\'' + escapeSimpleQuote(fileToConvert) + '\');" />');
	}

	function removeLogFileIcon(fileToConvert) {
		$(".action a[fileToConvert='" + fileToConvert + "']").siblings(".logFile").html('');
	}

	function writeProgress(fileToConvert, progress, resting) {
		$(".action a[fileToConvert='" + fileToConvert + "']").siblings(".progress").html(progress);
		$(".action a[fileToConvert='" + fileToConvert + "']").siblings(".resting").html(resting);
	}



	var intervals = new Array();
	var scheduledRefresh = 0;
	

	function checkVideo(fileToConvert) {
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/checkVideoConvertion.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				fileToConvert: fileToConvert 
			},
			success: function(data, textStatus, jqXHR) {
				// alert(data.status);
				if (data.status == 'RUNNING') {
					// L'encodage est en cours
					if (intervals[fileToConvert] == null) {
						intervals[fileToConvert] = window.setInterval("checkVideo('" + escapeSimpleQuote(fileToConvert) + "');" , 5000);
						scheduledRefresh++;
					}

					if (data.infos != undefined && data.infos['progress'] != undefined) {
						writeProgress(fileToConvert, data.infos['progress'], data.infos['resting']);
					} else {
						writeProgress(fileToConvert, '', '');
					}
					runningEncoding(fileToConvert);
				} else if (data.status == 'WARNING' || data.status == 'KO') {
					errorEncoding(fileToConvert, data.message);
					writeProgress(fileToConvert, '', '');
					window.clearInterval(intervals[fileToConvert]);
					scheduledRefresh--;
				} else if (data.status == 'OK') {
					endingEncoding(fileToConvert);
					writeProgress(fileToConvert, '', '');
					window.clearInterval(intervals[fileToConvert]);
					scheduledRefresh--;
				} else {
					errorEncoding(fileToConvert, "Le statut retourné est inconnu : " +
							data.status + ". " + data.message);
					writeProgress(fileToConvert, '', '');
					window.clearInterval(intervals[fileToConvert]);
					scheduledRefresh--;
				}

				// if (scheduledRefresh <= 0) {
				if ($(".action a img[src='style/images/loading.gif']").size() == 0) {
					enableConvertButtons();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				errorEncoding(fileToConvert, "Exception raised : " + jqXHR.responseText);
				window.clearInterval(intervals[fileToConvert]);
				scheduledRefresh--;

				if (scheduledRefresh == 0) {
					enableConvertButtons();
				}
				// La vidéo est toujours en cours d'encodage
			}
		});
		
	}


	var intervalLogFile = null;
	function seeLogFile(fileToConvert) {
		stopCheckLogFile();
		getLogFileContent(fileToConvert);
		
		$('#logDialog').dialog("open");
	}

	function stopCheckLogFile() {
		if (intervalLogFile != null) {
			window.clearInterval(intervalLogFile);
			intervalLogFile = null;
		}
	}

	function getLogFileContent(fileToConvert) {
		$('#logDialogContent').html('');
		$.ajax({
			type: 'POST',
			url: 'ajaxController/getLogFileContent.php',
			dataType : 'json',
			data: {
				fileToConvert: fileToConvert
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					$('#logDialogContent').html('Erreur lors de la récupération du contenu du fichier de log\n\n'
							+ '[' + data.status + '] ' + data.message);
				} else {
					var textArea = document.getElementById("logDialogContent");
					textArea.innerHTML = data.message;
					textArea.scrollTop = textArea.scrollHeight;
					if (intervalLogFile == null) {
						intervalLogFile = window.setInterval("getLogFileContent('" + escapeSimpleQuote(fileToConvert) + "');" , 3000);
					}
					var timeInfo = data.infos['time'] + " / " + data.infos['duration'] + " (" + data.infos['progress'] + ")";
					$('#logDialogTime').html(timeInfo);
				}

			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Erreur lors de la récupération du contenu du fichier de log : ' + jqXHR.responseText);
			}
		});
	}


	function complete(caller) {
		if ($(caller).children("img").attr('src') != "style/images/checked.png") {
			alert('Méthode complete : Pas la bonne icône');
			return false;
		}

		fileToComplete = $(caller).attr('fileToConvert');
		duree = null;
		if (getFileExtention(fileToComplete).toUpperCase() == "WEBM") {
			var duree = prompt("Saisissez la durée (en secondes) de la vidéo :", "")
			if (duree == null) {
				return false;
			}
			if (isNaN(duree)) {
				alert("Vous devez saisir un nombre entier");
				return complete(caller);
			}
		}

		$(caller).children("img").attr('src', "style/images/checked_background.png");
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/completeVideo.php',
			dataType : 'json',
			data: {
				fileToComplete: fileToComplete,
				duree : duree
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
					$(caller).children("img").attr('src', "style/images/checked.png");
				} else {
					$(caller).parents('tr').hide(500, function() {
						$(caller).parents('tr').remove();
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
				
				$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('src', 'style/images/error.png');
				$(".action a[fileToConvert='" + fileToConvert + "'] img").attr('title', 'Erreur lors du tranfert de la vidéo. Veuillez réactualiser la page.');
				$(".action a[fileToConvert='" + fileToConvert + "']").attr('action', 'error');
				removeLogFileIcon(fileToConvert);
				$(caller).children("img").attr('src', "style/images/checked.png");
			}
		});
	}

	function deleteFile(caller) {
		if (!confirm('Voulez-vous supprimer définitivement cette vidéo ?')) {
			return false;
		}

		var fileName = $(caller).attr('fileToDelete');
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=deleteRawVideo&nom=" + encodeURI(fileName)
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



	function convertAllVideos() {
		showLoadingPopup();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/convertAllVideo.php',
			dataType : 'json',
			data: {},
			success: function(data, textStatus, jqXHR) {
				
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue (convertAllVideos) : \n" + jqXHR.responseText);
			}
		});

		setTimeout(function () {
			$(".action a.boutonAction").each(function() {
				checkVideo(escapeSimpleQuote($(this).attr('fileToConvert')));
			});
			hideLoadingPopup();
		}, 3000);
	}






	

	

	


	$(document).ready(function() {
		$('#logDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 700,
			height : 500,
			close: function( event, ui ) { stopCheckLogFile(); }
		});

		$("#convertDialog").dialog({
			autoOpen: false,
			modal: true,
			width : 800,
			height : 345,
			resizable : false
		});

		$("#unlockVideos").button({
			icons: {
				primary: "ui-icon-unlocked"
			}
		}).click(function() {
			if (confirm('Voulez-vous débloquer les autres vidéos ?')) { 
				enableConvertButtons();
			}
		});

		$('#convertAllVideos').button({
			icons: {
				primary: "ui-icon-transferthick-e-w"
			}
		}).click(function() {
			if (confirm('Voulez-vous convertir toutes les vidéos ?\n' + 
					'ATTENTION cette opération prend du temps. Ne pas couper le serveur tant que toutes les vidéos ne sont pas encodées !!!')) { 
				convertAllVideos();
			}
		});

		
		
		
		<?php if ($freezeEncoding) { ?>
			disableConvertButtons();
			lockDisabling = true;
		<?php } ?>

		<?php foreach ($encodingVideos as $videoName => $video) { ?>
			checkVideo('<?= escapeSimpleQuote($videoName) ?>');
			// Toutes les 10 secondes => 10000
			/*
			intervals["<?= $videoName ?>"] = window.setInterval("checkVideo('<?= $videoName ?>');" , 5000);
			scheduledRefresh++;
			runningEncoding('<?= $videoName ?>');
			*/
		<?php } ?>

		<?php if ($freezeEncoding) { ?>
			lockDisabling = false;
		<?php } ?>


		$('#manageRawVideoTable').dataTable( {
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
	          ],
	        "iDisplayLength": -1
		});
		
	});


	jQuery.fn.dataTableExt.oSort['capacite-asc']  = function(a,b) {
		var capaciteA = getCapaciteInKo(a);
		var capaciteB = getCapaciteInKo(b);
		
	    return capaciteA - capaciteB;
	};
	
	jQuery.fn.dataTableExt.oSort['capacite-desc']  = function(a,b) {
		var capaciteA = getCapaciteInKo(a);
		var capaciteB = getCapaciteInKo(b);
		
	    return capaciteB - capaciteA;
	};

	
	
	
	</script>


<?php
include_once $pathToPhpRoot."pied.php";
?>