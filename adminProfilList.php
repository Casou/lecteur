<?php
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$profils = MetierProfil::getAllProfil();


?>
<div class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Liste des profils
	</div>
	<div class="blockContent">
		<ul>
			<?php foreach($profils as $profil) { ?>
				<li>
					<a href="#" onClick="adminProfil(<?= $profil->id ?>); return false;">
						<?= $profil->nom ?>
					</a>
				</li>
			<?php }?>
		</ul>
		
		<button id="newProfil">Nouveau profil</button>
	</div>
</div>



<script>


$('#newProfil').button( {
	icons: {
		primary: "ui-icon-circle-plus"
	}
}).click(function() {
	adminProfil(null);
});

function adminProfil(id) {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'adminProfil.php', 
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