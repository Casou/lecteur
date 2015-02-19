<?php
// Utilisable uniquement dans editVideoProperties.php
?>

<div id="allowEditVideoProfileUserDialog" title="Affecter la vidéo à des utilisateurs ou des profils">
	<table>
		<tr>
			<th colspan="3"><h2>Liste des profils</h2></th>
		</tr>
		<tr>
			<td>
				<select id="available_profile_select_edit" multiple="multiple" size="6">
					<?php foreach($profilesSelect as $profileSelect) { ?>
					<option value="<?= $profileSelect->id ?>"><?= $profileSelect->nom ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<a href="#" id="remove_allowed_profile_edit"><img src="style/images/fleche_gauche.png" /></a>
				<br/>
				<a href="#" id="add_allowed_profile_edit"><img src="style/images/fleche_droite.png" /></a>
			</td>
			<td>
				<select id="linked_profile_select_edit"  multiple="multiple" size="6">
				</select>
			</td>
		</tr>
		<tr>
			<th colspan="3"><h2>Liste des utilisateurs</h2></th>
		</tr>
		<tr>
			<td>
				<select id="available_user_select_edit" multiple="multiple" size="6">
					<?php foreach($usersSelect as $userSelect) { ?>
					<option value="<?= $userSelect->id ?>"><?= $userSelect->login ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<a href="#" id="remove_allowed_user_edit"><img src="style/images/fleche_gauche.png" /></a>
				<br/>
				<a href="#" id="add_allowed_user_edit"><img src="style/images/fleche_droite.png" /></a>
			</td>
			<td>
				<select id="linked_user_select_edit"  multiple="multiple" size="6">
				</select>
			</td>
		</tr>
	</table>
</div>



<script>
	var id_video_profile;

	function openEditVideoAllowProfileUserDialog(ids_profile, ids_user) {
		showLoadingPopup();
		$('#available_profile_select_edit').append($('#linked_profile_select_edit option'));
		$('#available_profile_select_edit option').attr('disabled', false);
		$('#available_user_select_edit').append($('#linked_user_select_edit option'));
		$('#available_user_select_edit option').attr('disabled', false);

		
		$(ids_profile).each(function() {
			var option = $('#visible_by_profile option[value=' + $(this)[0] + ']');
			$('#linked_profile_select_edit').append($('#available_profile_select_edit option[value=' + $(this)[0] + ']'));
			$('#linked_profile_select_edit option[value=' + $(this)[0] + ']').attr('disabled', option.attr('disabled') != undefined);
		});
		$(ids_user).each(function() {
			var option = $('#visible_by_user option[value=' + $(this)[0] + ']');
			$('#linked_user_select_edit').append($('#available_user_select_edit option[value=' + $(this)[0] + ']'));
			$('#linked_user_select_edit option[value=' + $(this)[0] + ']').attr('disabled', option.attr('disabled') != undefined);
		});

		$('#allowEditVideoProfileUserDialog').dialog('open');
		hideLoadingPopup();
	}


	$(document).ready(function() {
		$('#allowEditVideoProfileUserDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 405,
			resizable : false
		});


		$('#remove_allowed_profile_edit').click(function() {
			$('#linked_profile_select_edit option:selected').each(function() {
				$('#visible_by_profile option[value=' + $(this).val() + ']').remove();
			});
			$('#available_profile_select_edit').append($('#linked_profile_select_edit option:selected'));
			return false;
		});

		$('#add_allowed_profile_edit').click(function() {
			$('#visible_by_profile optgroup').append($('#available_profile_select_edit option:selected').clone());
			$('#linked_profile_select_edit').append($('#available_profile_select_edit option:selected'));
			return false;
		});

		$('#remove_allowed_user_edit').click(function() {
			$('#linked_user_select_edit option:selected').each(function() {
				$('#visible_by_user option[value=' + $(this).val() + ']').remove();
			});
			$('#available_user_select_edit').append($('#linked_user_select_edit option:selected'));
			return false;
		});

		$('#add_allowed_user_edit').click(function() {
			$('#visible_by_user optgroup').append($('#available_user_select_edit option:selected').clone());
			$('#linked_user_select_edit').append($('#available_user_select_edit option:selected'));
			return false;
		});

	});

</script>