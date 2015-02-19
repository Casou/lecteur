<?php
$usersSelect = MetierUser::getAllUser();
?>

<div id="allowUserDialog" style="display : none" title="Affecter des vidéos à des utilisateurs">
	<h2>Liste des utilisateurs</h2>
	<table>
		<tr>
			<td>
				<select id="available_user_select" multiple="multiple" size="6">
					<?php foreach($usersSelect as $userSelect) { ?>
					<option value="<?= $userSelect->id ?>"><?= $userSelect->login ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<a href="#" id="add_allowed_user"><img src="style/images/fleche_gauche.png" /></a>
				<br/>
				<a href="#" id="remove_allowed_user"><img src="style/images/fleche_droite.png" /></a>
			</td>
			<td>
				<select id="linked_user_select"  multiple="multiple" size="6">
				</select>
			</td>
		</tr>	
	</table>
	
	<button>Sauvegarder</button>
</div>

<script>
	var id_video_user;

	function openAllowUserDialog(ids) {
		showLoadingPopup();
		$('#available_user_select').append($('#linked_user_select option'));
		
		var ids_array = new Array();
		$(ids).each(function() {
			ids_array.push($(this).val());
		});

		if (ids_array.length == 0) {
			alert("Veuillez sélectionnner au moins une vidéo;");
			hideLoadingPopup();
			return;
		}

		
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageAllowedVideosController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				action : 'getVideosInfoForUsers',
				ids : ids_array
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				var users = data.infos['users'];
				for (var i = 0; i < users.length; i++) {
					$('#linked_user_select').append($('#available_user_select option[value=' + users[i] + ']'));
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		id_video_user = ids_array;
		$('#allowUserDialog').dialog('open');
		hideLoadingPopup();
	}


	function saveAllowedUser() {
		showLoadingPopup();
		var id_video_users_linked = new Array();
		$('#linked_user_select option').each(function() {
			id_video_users_linked.push($(this)[0].value);
		});
	
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageAllowedVideosController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				action : 'saveAllowedVideosForUsers',
				ids_video : id_video_user,
				users : id_video_users_linked
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				$('#allowUserDialog').dialog('close');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		hideLoadingPopup();
	}


	

	$(document).ready(function() {
		$('#allowUserDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 260,
			resizable : false
		});

		$("#allowUserDialog button").button({
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			saveAllowedUser();
		});

		$('#add_allowed_user').click(function() {
			$('#available_user_select').append($('#linked_user_select option:selected'));
			return false;
		});

		$('#remove_allowed_user').click(function() {
			$('#linked_user_select').append($('#available_user_select option:selected'));
			return false;
		});
	});

</script>