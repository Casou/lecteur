<?php

$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

$users = MetierUser::getAllUser();


?>

<div id="title">
	<h1>Administrer l'application</h1>
</div>

<div id="adminActions">
	<ul>
		<li><a href="#" onClick="adminActionList(); return false;">Actions</a></li>
		<li><a href="#" onClick="adminUserList(); return false;">Utilisateurs</a></li>
		<li><a href="#" onClick="adminProfilList(); return false;">Profils</a></li>
	</ul>
</div>

<div id="admin_left_div">
</div>


<div id="editDiv">

</div>





<script>

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


function adminActionList() {
	$('#editDiv').html('');
	$.ajax({
		type: 'POST', 
		url: 'adminActionList.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#admin_left_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}


function adminUserList() {
	$('#editDiv').html('');
	$.ajax({
		type: 'POST', 
		url: 'adminUserList.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#admin_left_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}

function adminProfilList() {
	$('#editDiv').html('');
	$.ajax({
		type: 'POST', 
		url: 'adminProfilList.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#admin_left_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}

/*
adminActionList();
adminUserList();
adminProfilList();
*/

</script>


<?php 
include $pathToPhpRoot.'pied.php'; 
?>