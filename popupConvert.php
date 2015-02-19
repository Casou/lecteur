<?php

?>

<div id="convertDialog" style="display : none" title="Options de conversions">
	<form action="#" onSubmit="return false;">
		<input type="hidden" name="fileToConvert" value="" id="fileToConvertHidden" />
		
		<h2>Fichier : <span class="fileName" style="font-style : italic;"></span></h2>
		
		<div>
			<h3>Format d'entrée :</h3>
			<ul style="list-style-type : none; margin : 0; padding : 0;">
				<li>
					<input type="radio" name=convertFormat id="convertOGV" value="ogv" 
						onChange="if (this.checked) setCommand('OGV');" />
						<label for="convertOGV">Format OGV</label>
				</li>
				<li>
					<input type="radio" name="convertFormat" id="convertAVI" value="avi" 
						onChange="if (this.checked) setCommand('AVI');" />
						<label for="convertAVI">Format AVI</label>
				</li>
				<li>
					<input type="radio" name="convertFormat" id="convertMOV" value="mov" 
						onChange="if (this.checked) setCommand('MOV');" />
						<label for="convertMOV">Format MOV</label>
				</li>
				<li>
					<input type="radio" name="convertFormat" id="convertMP4" value="mp4" 
						onChange="if (this.checked) setCommand('MP4');" />
						<label for="convertMP4">Format MP4</label>
				</li>
				<li>
					<input type="radio" name="convertFormat" id="convertWEBM" value="webm" 
						onChange="if (this.checked) setCommand('WEBM');" />
						<label for="convertWEBM">Format WEBM</label>
				</li>
				<li>
					<input type="radio" name="convertFormat" id="convertCustom" value="custom" />
						<label for="convertCustom">Personnalisé</label>
				</li>
			</ul>
		</div>

		<div style="margin-top : 10px;">
			<h3>Commande</h3>
			<span class="convertCommandFix">ffmpeg.exe -i [fileName]</span>
			<span class="convertCommandInput">
				<input type="text" name="command" id="convertCommand" />
			</span>
			<span class="convertCommandFix">[outputFile] 2&gt;[fileName].log</span>
		</div>
		<button id="convertDialogButton">Convertir</button>
		
	</form>
	
</div>

<script>
	var commands = { 
			"OGV" : '-loglevel info -f webm -vcodec libvpx -acodec libvorbis -ab 160000 -crf 22 -y', 
			"AVI" : '-loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y',
			"MOV" : '-loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y',
			"MP4" : '-loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y',
			"WEBM" : '-loglevel info -acodec libvorbis -ac 2 -ab 96k -ar 44100 -b 345k -y'};


	function setCommand(command) {
		$('#convertCommand').val(commands[command]);
	}

	function launchConvert(fileToConvert, command, videoLength) {
		
		runningEncoding(fileToConvert);
		disableConvertButtons();

		$("#convertDialog").dialog('close');
		
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/convertVideo.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				fileToConvert: fileToConvert,
				command : command,
				videoLength : videoLength
			},
			success: function(data, textStatus, jqXHR) {
				/*
				if (data.status == 'RUNNING') {
					// L'encodage est en cours, on ne fait rien
				} else if (data.status == 'WARNING' || data.status == 'KO') {
					errorEncoding(fileToConvert, data.message);
				} else if (data.status == 'OK') {
					endingEncoding(fileToConvert);
				} else {
					errorEncoding(fileToConvert, "Le statut retourné est inconnu : " +
							data.status + ". " + data.message);
				}

				enableConvertButtons();
				
				/*
				$('.openDialog').click(function (){
					$("#dialog").dialog('open');
				});
				*/
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);

				errorEncoding(fileToConvert, "Exception raised : " + jqXHR.responseText);
				window.clearInterval(intervals[fileToConvert]);
				
				enableConvertButtons();

				convertionLocked = false;
			}
		});

		setTimeout(function () {
			checkVideo(escapeSimpleQuote(fileToConvert));
		}, 3000);
	}

	

	$(document).ready(function() {
		$("#convertDialogButton").button({
			icons: {
				primary: "ui-icon-transferthick-e-w"
			}
		}).click(function() {
			launchConvert($('#fileToConvertHidden').val(), 
					$('#convertCommand').val(), 
					$('#videoLength').val());
		});
	});
</script>