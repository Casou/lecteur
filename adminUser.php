<?php
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

if (isset($_POST['id']) && $_POST['id'] != null) {
	$id = $_POST['id'];
	$userDTO = MetierUser::getUserById($id);
	$currentUser = $userDTO->user;
} else {
	$userDTO = new UserDTO();
	$currentUser = new User();
}

$droits_categories = MetierDroitCategorie::getAllDroitByCategories();
$danses = MetierDanse::getAllDanse(true);
$profils = MetierProfil::getAllProfil();
$profils_linked = array();
foreach($userDTO->profils as $profil) {
	$profils_linked[] = $profil->id;
}

?>

<div class="bouton_retour">
	<img src="style/images/fleche_gauche.png" />
	<a href="#" onClick="adminUserList(); return false;">Retour aux utilisateurs</a>
</div>

<div id="admin_user" class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		<?php if($currentUser->id != null) { ?>
			Modification de <?= $currentUser->login ?>
		<?php } else { ?>
			Création d'un nouvel utilisateur
		<?php } ?>
	</div>
	<div class="blockContent">
		<form action="#" method="post" id="formUsers" onSubmit="return false;">
			<input type="hidden" name="id" value="<?= $currentUser->id ?>" />
			<input type="hidden" name="action" value="editUser" />
			
			
			<div>
				<label for="name">Login : </label>
				<input type="text" name="login" id="name" value="<?= $currentUser->login ?>" />
			</div>
			<div>
				<label for="password">Mot de passe : </label>
				<input type="text" name="password" id="password" value="<?= $currentUser->password ?>" />
			</div>
			<div>
				<label for="log_level">Niveau de log : </label>
				<select name="log_level" id="log_level">
					<option value="" id="log_level_select_default_option">--Défaut--</option>
					<?php foreach($LOG_LEVELS_MAP as $key => $label) { 
						$selected = "";
						if ($key == $currentUser->log_level) {
							$selected = "selected";
						}
					?>
					<option value="<?= $key ?>" <?= $selected ?>><?= $label ?></option>
					<?php } ?>
				</select>
			</div>
			
			<div id="edit_user_profil">
				<h2>Profils vidéo : </h2>
				<div>
					<select id="profil_available" multiple="multiple" size="6">
						<?php if (count($profils) > 0) { 
							foreach($profils as $profil) { 
								if (in_array($profil->id, $profils_linked)) {
									continue;
								}
						?>
							<option value="<?= $profil->id ?>"><?= $profil->nom ?></option>
						<?php 	}
							}?>
					</select>
				</div>
				
				<div>
					<a href="#" id="remove_profil_user">
						<img src="style/images/fleche_haut.png" title="Retirer" />
					</a>
					<a href="#" id="add_profil_user">
						<img src="style/images/fleche_bas.png" title="Ajouter" />
					</a>
				</div>
				
				<div>
					<select id="profil_linked" name="profils[]" multiple="multiple" size="6">
						<?php if (count($userDTO->profils) > 0) { 
							foreach($userDTO->profils as $profil) { ?>
							<option value="<?= $profil->id ?>"><?= $profil->nom ?></option>
						<?php 	}
							}?>
					</select>
				</div>
				
				<?php if ($currentUser->id != null) { ?>
				<button id="allowed_videos_button">Liste des vidéos visibles</button>
				<button id="refresh_allowed_videos_button">Actualiser les vidéos visibles</button>
				<?php } ?>
			</div>
			
			<div>
				<h2>Droits : </h2>
				
				<?php foreach ($droits_categories as $droit_categorie_dto) { 
					$droit_categorie = $droit_categorie_dto->droit_categorie;
					$droits = $droit_categorie_dto->droits;
				?>
				<h3><?= $droit_categorie->nom ?></h3>
				<ul>
					<?php foreach($droits as $droit) { 
						$checked = "";
						if (in_array($droit->nom, $userDTO->droitsNom)) {
							$checked = 'checked="checked"';
						}
					?>
					<li>
						<input type="checkbox" name="droits[]" id="droit_<?= $droit->id ?>" 
							<?= $checked ?> value="<?= $droit->id ?>" />
						<label for="droit_<?= $droit->id ?>">
						<?= $droit->label ?>
						</label>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
				
			</div>
		</form>
		
		<button id="saveUser">Sauvegarder</button>
		
		<?php if($currentUser->id != null) { ?>
		<button id="deleteUser" style="margin-left : 20px;">Supprimer</button>
		<?php } ?>
	</div>
</div>

<div id="allowed_videos_dialog" title="Vidéos visibles par l'utilisateur">


</div>





<script>

function saveUser() {
	showLoadingPopup();
	$('#profil_available option').prop('selected', false);
	$('#profil_linked option').prop('selected', true);
	
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
				adminUserList();
				// adminUser(data.infos['id']);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
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
				adminUserList();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}


function allowedVideosForUser() {
	showLoadingPopup();
	$.ajax({
		type: 'POST',
		url: 'listeEvenements.php',
		dataType : 'html',
		async : false,
		data: {
			id_user_monitored : <?= $currentUser->id == null ? "null" : $currentUser->id ?>
		},
		success: function(data, textStatus, jqXHR) {
			$('#allowed_videos_dialog').html(data);
			$('#allowed_videos_dialog').dialog('open');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}


function refreshAllowedVideosForUser() {
	showLoadingPopup();
	$.ajax({
		type: 'POST',
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : "action=generateTmpForUser",
			id_user : <?= $currentUser->id == null ? "null" : $currentUser->id ?>
		},
		success: function(data, textStatus, jqXHR) {
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}









$(document).ready(function() {
	$('#saveUser').button( {
		icons: {
			secondary : "ui-icon-disk"
		}
	}).click(function() {
		saveUser();
	});

	$('#allowed_videos_button').button( {
		icons: {
			primary : "ui-icon-search"
		}
	}).click(function() {
		allowedVideosForUser();
	});

	$('#refresh_allowed_videos_button').button( {
		icons: {
			primary : "ui-icon-refresh"
		},
		text : false
	}).click(function() {
		refreshAllowedVideosForUser();
	});
	

	<?php if($currentUser->id != null) { ?>
	$('#deleteUser').button( {
		icons: {
			secondary : "ui-icon-trash"
		}
	}).click(function() {
		deleteUser(<?= $currentUser->id ?>);
	});
	<?php } ?>

	$('#add_profil_user').click(function() {
		$('#profil_linked').append($('#profil_available option:selected'));
		return false;
	});

	$('#remove_profil_user').click(function() {
		$('#profil_available').append($('#profil_linked option:selected'));
		return false;
	});

	$('#allowed_videos_dialog').dialog({
		autoOpen: false,
		modal: true,
		width : '80%',
		height : 600
	});
	
});

</script>
