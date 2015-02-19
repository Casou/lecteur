<?php

include_once "includes.php";
Logger::init(LOG_FILE_NAME);

$users = MetierUser::getAllUser();

?>

<div class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Liste des utilisateurs
	</div>
	<div class="blockContent">
		<form action="#" method="post" id="formUsers" onSubmit="return false;">
			<input type="hidden" name="action" value="editUsers" />
			
			<table id="adminUser">
				<thead>
					<tr>
						<th>Login</th>
						<th>Password</th>
						<th>Suppr.</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($users as $user) { ?>
					<tr>
						<td>
							<input type="text" name="login_<?= $user->id ?>" value="<?= $user->login ?>" />
						</td>
						<td>
							<input type="text" name="password_<?= $user->id ?>" value="<?= $user->password ?>" />
						</td>
						<td>
							<a href="#" onClick="deleteUser(<?= $user->id ?>);">
								<img src="style/images/delete.png" alt="Suppr" alt="Supprimer la vidéo" />
							</a>
						</td>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</form>
		
		<button id="newUser">Nouvel utilisateur</button>
		<button id="saveUser">Sauvegarder</button>
	</div>
</div>

<div id="popupNewUser" style="display : none;" title="Nouvel utilisateur">
	<form action="#" method="post" id="formNewUser" onSubmit="return false;">
		<input type="hidden" name="action" value="newUser" />
		<table>
			<tr>
				<th>Login</th>
				<td><input type="text" name="login" /></td>
			</tr>
			<tr>
				<th>Password</th>
				<td><input type="text" name="password" /></td>
			</tr>
		</table>
		<button id="addNewUser" style="float : right; margin-top : 10px;">Ajouter</button>
	</form>
</div>


<script>

function saveUser() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'ajaxController/manageAdminController.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		async : false,
		data: {
			formulaire : $('#formUsers').serialize()
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				// On ne fait rien.
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	refreshAdmin();
	hideLoadingPopup();	
}

function newUser() {
	showLoadingPopup();
	var isOk = true;
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'ajaxController/manageAdminController.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		async : false,
		data: {
			formulaire : $('#formNewUser').serialize()
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
				isOk = false;
			} else {
				// On ne fait rien.
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			isOk = false;
		}
	});

	refreshAdmin();
	$('#popupNewUser').dialog("close");

	hideLoadingPopup();
}


function deleteUser(idUser) {
	if (!confirm('Etes-vous sûr de vouloir supprimer cet utilisateur ?')) {
		return false;
	}

	showLoadingPopup();

	$.ajax({
		type: 'POST',
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : "action=deleteUser&id=" + idUser
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				// On ne fait rien.
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	refreshAdmin();
	hideLoadingPopup();
}
















$(document).ready(function() {
	$('#newUser').button( {
		icons: {
			primary: "ui-icon-circle-plus"
		}
	}).click(function() {
		$('#popupNewUser').dialog("open");
	});

	$('#addNewUser').button( {
		icons: {
			primary: "ui-icon-circle-plus"
		}
	}).click(function() {
		newUser();
	});
	
	$('#saveUser').button( {
		icons: {
			secondary: "ui-icon-disk"
		}
	}).click(function() {
		saveUser();
	});

	$('#popupNewUser').dialog({
		autoOpen: false,
		modal: true,
		width : 300,
		height : 140,
		resizable : false
	});
});

</script>
