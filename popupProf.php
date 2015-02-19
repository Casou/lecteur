<?php
$professeurs = MetierProfesseur::getAllProfesseur();
?>

<div id="profDialog" style="display : none" title="Gérer les professeurs">
	<form action="#" onSubmit="return false;">
		<input type="hidden" name="action" value="addProfesseur" />
		
		Nom du professeur : 
		<input type="text" name="nom" />
		<button>Ajouter</button>
		
	</form>
	
	<h2>Liste des professeurs</h2>
	<table id="tableProfs" class="manageTablePopup">
	<?php foreach($professeurs as $prof) { ?>
		<tr id="professeur_<?= $prof->id ?>">
			<td>
				<input type='text' class="noBorder" value='<?= escapeSimpleQuoteHTML($prof->nom) ?>'
					onFocus="$(this).removeClass('noBorder');"
					onBlur="$(this).addClass('noBorder');" 
					onChange="changeNomProfesseur(this, <?= $prof->id ?>);" /></td>
			<td>
				<a href="#" onClick="removeProfesseur(<?= $prof->id ?>); return false;">
					<img src="style/images/delete.png" />
				</a>
			</td>
		</tr>
	<?php } ?>
	</table>
</div>

<script>
	function addProfesseur() {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : $('#profDialog form').serialize()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var newId = data.infos['id'];
					$('#tableProfs').append(
						'<tr id="professeur_' + newId +'">' +
							'<td>' + 
								'<input type="text" class="noBorder" value="' + $('#profDialog form input[name=nom]').val() + '" '+
									'onFocus="$(this).removeClass(\'noBorder\');"' +
									'onBlur="$(this).addClass(\'noBorder\');"' + 
									'onChange="changeNomProfesseur(this, ' + newId + ');" ' + 
							'</td>' +
							'<td><a href="#" onClick="removeProfesseur(' + newId + '); return false;">' +
								'<img src="style/images/delete.png" /></a>' + 
							'</td>' +
						'</tr>'
					);
					$('#profDialog form input[name=nom]').val('');
					updateProfesseurs();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function changeNomProfesseur(input, id) {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=changeNomProfesseur&id=" + id + "&nom=" + $(input).val()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					// Si tout se passe bien, on ne fait rien.
					updateProfesseurs();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function removeProfesseur(id) {
		if (!confirm('Voulez-vous supprimer ce professeur ?')) { 
			return false;
		}
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=removeProfesseur&id=" + id
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					$('#professeur_' + id).hide('slow', function() {
						$('#professeur_' + id).remove();
					});
					updateProfesseurs();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	$(document).ready(function() {
		$('#profDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 430,
			resizable : false
		});

		$("#profDialog button").button({
			icons: {
				primary: "ui-icon-circle-plus"
			}
		}).click(function() {
			if (confirm('Voulez-vous ajouter ce professeur ?')) {
				addProfesseur();
			}
		});
	});

</script>