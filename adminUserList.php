<?php
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$users = MetierUser::getAllUser();


?>
<div class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Liste des utilisateurs
	</div>
	<div class="blockContent">
		<ul>
			<?php foreach($users as $user) { ?>
				<li>
					<a href="#" onClick="adminUser(<?= $user->id ?>); return false;">
						<?= $user->login ?>
					</a>
				</li>
			<?php }?>
		</ul>
		
		<button id="newUser">Nouvel utilisateur</button>
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
		data: {
			id : id
		},
		success: function(data, textStatus, jqXHR) {
			$('#editDiv').html(data);
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
		}
	});
}

</script>


<?php 
Logger::close();
Database::disconnectDB();
?>