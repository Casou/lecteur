<?php
$danses = MetierDanse::getAllDanse(true);
?>

<div id="danseDialog" style="display : none" title="Gérer les danses">
	<form action="#" onSubmit="return false;">
		<input type="hidden" name="action" value="addDanse" />
		
		Nom de la danse : 
		<input type="text" name="nom" />
		<button>Ajouter</button>
		
	</form>
	
	<h2>Liste des danses</h2>
	<table id="tableDanses" class="manageTablePopup">
	<?php foreach($danses as $danse) { ?>
		<tr id="danse_<?= $danse->id ?>">
			<td>
				<input type='text' class="noBorder" value='<?= escapeSimpleQuoteHTML($danse->nom) ?>'
					onFocus="$(this).removeClass('noBorder');"
					onBlur="$(this).addClass('noBorder');" 
					onChange="changeNomDanse(this, <?= $danse->id ?>);" /></td>
			<td>
				<a href="#" onClick="removeDanse(<?= $danse->id ?>); return false;">
					<img src="style/images/delete.png" />
				</a>
			</td>
		</tr>
	<?php } ?>
	</table>
</div>

<script>
	function addDanse() {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : $('#danseDialog form').serialize()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var newId = data.infos['id'];
					$('#tableDanses').append(
						'<tr id="danse_' + newId +'">' +
							'<td>' + 
								'<input type="text" class="noBorder" value="' + $('#danseDialog form input[name=nom]').val() + '" '+
									'onFocus="$(this).removeClass(\'noBorder\');"' +
									'onBlur="$(this).addClass(\'noBorder\');"' + 
									'onChange="changeNomDanse(this, ' + newId + ');" ' + 
							'</td>' +
							'<td><a href="#" onClick="removeDanse(' + newId + '); return false;">' +
								'<img src="style/images/delete.png" /></a>' + 
							'</td>' +
						'</tr>'
					);
					$('#danseDialog form input[name=nom]').val('');

					updateDanses();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function changeNomDanse(input, id) {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=changeNomDanse&id=" + id + "&nom=" + $(input).val()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					// Si tout se passe bien, on ne fait rien.
					updateDanses();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function removeDanse(id) {
		if (!confirm('Voulez-vous supprimer cette danse ?')) { 
			return false;
		}
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=removeDanse&id=" + id
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					$('#danse_' + id).hide('slow', function() {
						$('#danse_' + id).remove();
					});
					updateDanses();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	$(document).ready(function() {
		$('#danseDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 430,
			resizable : false
		});

		$("#danseDialog button").button({
			icons: {
				primary: "ui-icon-circle-plus"
			}
		}).click(function() {
			if (confirm('Voulez-vous ajouter cette danse ?')) { 
				addDanse();
			}
		});
	});

</script>