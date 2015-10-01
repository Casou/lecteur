<?php

$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

$users = MetierUser::getAllUser();


?>

<div id="title" class="admin_title">
	<h1>Administrer l'application</h1>
</div>

<div id="adminActions">
	<ul>
		<li><a href="#" onClick="adminActionList(); return false;">Actions</a></li>
		<li><a href="#" onClick="adminUserList(); return false;">Utilisateurs</a></li>
		<li><a href="#" onClick="adminProfilList(); return false;">Profils</a></li>
	</ul>
</div>

<div id="content_div">

</div>





<script>


function adminActionList() {
	showLoadingPopup();
	$('#content_div').html('');
	$.ajax({
		type: 'POST', 
		url: 'adminActionList.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#content_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}


function adminUserList() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'adminUserList.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#content_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}

function adminProfilList() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'adminProfilList.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#content_div').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
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