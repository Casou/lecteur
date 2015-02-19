<?php

include_once "includes.php";
Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$usersDTO = MetierUser::getAllUserDTO();
$droits = MetierDroit::getAllDroit();

?>

<div class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Droits utilisateurs
	</div>
	<div class="blockContent">
		<form action="#" method="post" id="formDroits" onSubmit="return false;">
			<input type="hidden" name="action" value="editDroit" />
			<table id="adminDroit">
				<thead>
					<tr>
					<th class="noBorder" colspan="2" rowspan="2">
						<button id="submitDroits">Valider</button>
					</th>
					<th colspan="<?= count($usersDTO) ?>">Utilisateurs</th>
					</tr>
					<tr>
						<?php foreach($usersDTO as $dto) { ?>
						<th><?= $dto->user->login ?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="enteteDroit" rowspan="<?= count($droits) + 1 ?>">Droits</th>
					</tr>
				<?php foreach($droits as $droit) { ?>
					<tr>
						<td><?= $droit->label ?></td>
					
					<?php foreach($usersDTO as $dto) {
						$checked = "";
						if (in_array($droit->nom, $dto->droitsNom)) {
							$checked = 'checked="checked"';
						} 
					?>
						<td class="checkbox">
							<input type="checkbox" name="<?= $dto->user->id?>_<?= $droit->nom ?>" 
								<?= $checked ?> />
						</td>
					<?php }?>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</form>
	</div>
</div>


<script>

function saveDroit() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'ajaxController/manageAdminController.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		data: {
			formulaire : $('#formDroits').serialize()
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				// On ne fait rien.
			}
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();	
		}
	});
}

$(document).ready(function() {
	$('#submitDroits').button( {
		icons: {
			secondary: "ui-icon-disk"
		}
	}).click(function() {
		saveDroit();
	});
});


</script>
