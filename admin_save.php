<?php

include_once 'entete.php';

?>

<div id="title">
	<h1>Administrer l'application</h1>
</div>

<div id="adminUsersDiv">
	<?php include_once 'adminUsers.php'; ?>
</div>

<div id="adminDroitsDiv">
	<?php include_once 'adminDroits.php'; ?>
</div>




<script>

function refreshAdmin() {
	// On met à jour adminDroits
	$.ajax({
		type: 'POST', 
		url: 'adminDroits.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#adminDroitsDiv').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue (récupération d'adminDroits.php) : \n" + jqXHR.responseText);
			isOk = false;
		}
	});


	// On met à jour adminUsers
	$.ajax({
		type: 'POST', 
		url: 'adminUsers.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#adminUsersDiv').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue (récupération d'adminDroits.php) : \n" + jqXHR.responseText);
			isOk = false;
		}
	});
}

</script>


<?php 
include 'pied.php'; 
?>