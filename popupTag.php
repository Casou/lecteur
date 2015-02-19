<?php
$tags = MetierTag::getAllTag();
?>

<div id="tagDialog" style="display : none" title="Gérer les tags">
	<form action="#" onSubmit="return false;">
		<input type="hidden" name="action" value="addTag" />
		
		Label : 
		<input type="text" name="label" />
		<button>Ajouter</button>
		
	</form>
	
	<h2>Liste des tags</h2>
	<table id="tableTags" class="manageTablePopup">
	<?php foreach($tags as $tag) { ?>
		<tr id="tag_<?= $tag->id ?>">
			<td>
				<input type='text' class="noBorder" value='<?= escapeSimpleQuoteHTML($tag->label) ?>'
					onFocus="$(this).removeClass('noBorder');"
					onBlur="$(this).addClass('noBorder');" 
					onChange="changeLabelTag(this, <?= $tag->id ?>);" /></td>
			<td>
				<a href="#" onClick="removeTag(<?= $tag->id ?>); return false;">
					<img src="style/images/delete.png" />
				</a>
			</td>
		</tr>
	<?php } ?>
	</table>
</div>

<script>
	function addTag() {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : $('#tagDialog form').serialize()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var newId = data.infos['id'];
					$('#tableTags').append(
						'<tr id="tag_' + newId +'">' +
							'<td>' + 
								'<input type="text" class="noBorder" value="' + $('#tagDialog form input[name=label]').val() + '" '+
									'onFocus="$(this).removeClass(\'noBorder\');"' +
									'onBlur="$(this).addClass(\'noBorder\');"' + 
									'onChange="changeLabelTag(this, ' + newId + ');" ' + 
							'</td>' +
							'<td><a href="#" onClick="removeTag(' + newId + '); return false;">' +
								'<img src="style/images/delete.png" /></a>' + 
							'</td>' +
						'</tr>'
					);
					$('#tagDialog form input[name=label]').val('');
					updateTags();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function changeLabelTag(input, id) {
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=changeLabelTag&id=" + id + "&label=" + $(input).val()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					// Si tout se passe bien, on ne fait rien.
				}
				updateTags();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function removeTag(id) {
		if (!confirm('Voulez-vous supprimer ce tag ?')) { 
			return false;
		}
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=removeTag&id=" + id
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					$('#tag_' + id).hide('slow', function() {
						$('#tag_' + id).remove();
					});
					updateTags();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	$(document).ready(function() {
		$('#tagDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 430,
			resizable : false
		});

		$("#tagDialog button").button({
			icons: {
				primary: "ui-icon-circle-plus"
			}
		}).click(function() {
			if (confirm('Voulez-vous ajouter ce tag ?')) {
				addTag();
			}
		});
	});

</script>