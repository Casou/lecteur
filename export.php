<?php
$pathToPhpRoot = './';
include_once 'entete.php';

?>

<div id="title" class="export_script">
	<h1>Export de script SQL</h1>
</div>

<main id="export_script">
	<div id="exportDiv" class="ui-widget ui-corner-all block">
		<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
			Génération du script
		</div>
		<div class="blockContent">
			<div>
				<label for="exportMinId">Exporter à partir de l'id : </label>
				<input type="text" name="exportMinId" id="exportMinId" value="" size="12"  
					maxlength="4" placeholder="Borne inf (req)" />
				<input type="text" name="exportMaxId" id="exportMaxId" value="" size="12"
					maxlength="4" placeholder="Borne sup" />
			</div>
			
			<div>
				<a href="#" id="generate">Générer</a>
			</div>
	
			<div>
				<button id="selectQueries">Sélectionner tout le texte</button>
			</div>
			<div id="resultSqlScript">
				<textarea placeholder="Résultat" readonly="readonly" id="resultSql"></textarea>
			</div>
			
		</div>
	</div>
</main>



<script>

function generateExport() {
	if ($('#exportMinId').val().trim() == "") {
		alert("La borne inférieure est obligatoire.");
		return false;
	}

	if (isNaN($('#exportMinId').val()) || isNaN($('#exportMaxId').val())) {
		alert("Les bornes doivent être des entiers positifs.");
		return false;
	}
	
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/generateExportScriptController.php', 
		dataType : 'json',
		data: {
			exportMinId : $('#exportMinId').val(),
			exportMaxId : $('#exportMaxId').val()
		},
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#resultSql').html(data.message);
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
		}
	});
}

$(document).ready(function() {
	$('#generate').button( {
		icons: {
			secondary: "ui-icon-refresh"
		}
	}).click(function() {
		generateExport();
	});
});

$( "#selectQueries" ).button({
	icons: {
		primary : "ui-icon-copy"
	}
}).click(function() {
	$('#resultSql').focus();
	$('#resultSql').select();
});;

</script>


<?php 
include 'pied.php'; 
?>