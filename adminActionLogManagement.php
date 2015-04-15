<?php
session_start();

$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$users = MetierUser::getAllUser();
$default_log_level = FwkParameter::getParameter(PARAM_CONTEXT_LOG, PARAM_ID_LOG_DEFAULT_LEVEL);

?>

<div id="admin_action_log_level" class="admin_action_log_management" class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Niveau de logs par défaut
	</div>
	<div class="blockContent">
		<form action="#" onSubmit="return false;">
			<input type="hidden" name="action" value="saveDefaultLogLevel" />
			<ul>
			<?php foreach($LOG_LEVELS_MAP as $key => $label) { 
				$checked = "";
				if ($key == $default_log_level) {
					$checked = "checked='checked'";
				}
			?>
				<li>
					<input type="radio" name="default_log_level" value="<?= $key ?>" id="default_log_<?= $key ?>" <?= $checked ?>/>
					<label for="default_log_<?= $key ?>"><?= $label ?></label>
				</li>
			<?php } ?>
			</ul>
		</form>
		
		<button>Modifier le niveau par défaut</button> 
	</div>
</div>

<div id="admin_action_log_delete" class="admin_action_log_management" class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Supprimer les logs des utilisateurs
	</div>
	<div class="blockContent">
		<form action="#" onSubmit="return false;">
			<input type="hidden" name="action" value="deleteLog" />
			<div id="todays_log">
				<input type="checkbox" name="todays_log" id="todays_log_input" />
				<label for="todays_log_input">Log du jour uniquement</label>
			</div>
			<ul>
				<?php foreach($users as $user) { ?>
				<li>
					<input type="checkbox" name="userToPurge[]" value="<?= $user->login ?>" id="user_<?= $user->id ?>" />
					<label for="user_<?= $user->id ?>"><?= $user->login ?></label>
				</li>
				<?php } ?>
			</ul>
		</form>
		<a href="#" onClick="selectAll(true); return false;">Tout sélectionner</a> /
		<a href="#" onClick="selectAll(false); return false;">Tout désélectionner</a>
		
		<button>Supprimer les logs</button>
	</div>
</div>

<script>

function selectAll(isChecked) {
	$('#admin_action_log_delete li input').prop('checked', isChecked); 
}

function saveDefaultLogLevel() {
	showLoadingPopup();
	$.ajax({
		type: 'POST',
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data : {
			formulaire : $('#admin_action_log_level form').serialize()
		},
		success: function(data, textStatus, jqXHR) {
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}

function deleteAllLog() {
	if (!confirm('Etes-vous sûr de vouloir supprimer tous les fichiers de logs ?')) {
		return;
	}
	showLoadingPopup();
	$.ajax({
		type: 'POST',
		url: 'ajaxController/manageAdminController.php',
		dataType : 'html',
		data : {
			formulaire : $('#admin_action_log_delete form').serialize()
		},
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#admin_action_log_delete input[type=checkbox]').attr('checked', false);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}


$(document).ready(function() {
	$('#admin_action_log_level button').button( {
		icons: {
			secondary: "ui-icon-disk"
		}
	}).click(function() {
		saveDefaultLogLevel();
	});


	$('#admin_action_log_delete button').button( {
		icons: {
			secondary: "ui-icon-trash"
		}
	}).click(function() {
		deleteAllLog();
	});
});

</script>


<?php 
Logger::close();
?>