<?php
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$users = MetierUser::getAllUserDTO();

?>
<div id="user_list_div">
	<div class="ui-widget ui-corner-all block">
		<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
			Liste des utilisateurs
		</div>
		<div class="blockContent">
			<table>
				<tr>
					<th>Login</th>
					<th>Profils vidéo</th>
					<th title="Dernière connexion">Der. conn.</th>
					<th>Niv. log</th>
					<th>Actions</th>
				</tr>
				<?php 
				foreach($users as $userDTO) { 
					$user = $userDTO->user;
					$profils = $userDTO->profils;
					if ($profils == null) {
						$profils = array();
					}
					$logConnexion = $userDTO->logConnexion;
					$lastConnexion = "--";
					if ($logConnexion != null && count($logConnexion) > 0) {
						$titleLog = "";
						$lastConnexion = "";
						foreach($logConnexion as $log) {
							$titleLog .= Fwk::parseDate($log->date, "d/m/Y H:i:s")." : ".$log->ip."\n";
							if ($lastConnexion == "") {
								$lastConnexion = 
									"<span class='user_list_connection_date'>".
										Fwk::parseDate($log->date, "d/m/Y").
									"</span> ".
									"<span class='user_list_connection_hour'>".
										Fwk::parseDate($log->date, "H:i:s").
									"</span>".
									"<span class='user_list_connection_ip'> : ".$log->ip."</span>";
							}
						}
						$lastConnexion = "<span title='$titleLog'>$lastConnexion</span>";
					}
					
					$is_admin = false;
					$first =  true;
					$profilString = "";
					foreach($profils as $profil) {
						if (!$first) {
							$profilString .= ", ";
						}
						if ($profil->nom == 'Admin') {
							$is_admin = true;
						}
						$profilString .= $profil->nom;
						$first = false;
					}
					
					$log_level = ($user->log_level == null) ? "<i>Défaut</i>" : $LOG_LEVELS_MAP[$user->log_level];
				?>
					<tr>
						<td class="user_list_login">
							<?php if ($is_admin) { ?>
								<img src="style/images/user_purple.png" alt="User" alt="Utilisateur" class="img_admin" />
							<?php } else { ?>
								<img src="style/images/user_blue.png" alt="User" alt="Utilisateur" class="img_user" />
							<?php } ?>
							<?= $user->login ?>
						</td>
						<td class="user_list_profile">
							<?= $profilString ?>
						</td>
						<td class="user_list_connection"><?= $lastConnexion ?></td>
						<td class="user_log_level"><?= $log_level ?></td>
						<td class="user_list_edit">
							<a href="#" onClick="adminUser(<?= $user->id ?>); return false;">
								<img src="style/images/param_mini.png" alt="Edit" alt="Editer les propriétés de l'utilisateur" />
							</a>
							<a href="showLog.php?user_login=<?= $user->login ?>" target="_blank">
								<img src="style/images/log_file_mini.png" alt="Show Log" alt="Montrer les logs de l'utilisateur" />
							</a>
						</td>
					</tr>
				<?php }?>
			</table>
			
			<button id="newUser">Nouvel utilisateur</button>
		</div>
	</div>
</div>

<script>


$('#newUser').button( {
	icons: {
		primary: "ui-icon-circle-plus"
	}
}).click(function() {
	adminUser(null);
});

function adminUser(id) {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'adminUser.php', 
		dataType : 'html',
		async : false,
		data: {
			id : id
		},
		success: function(data, textStatus, jqXHR) {
			$('#user_list_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}

</script>


<?php 
Logger::close();
Database::disconnectDB();
?>