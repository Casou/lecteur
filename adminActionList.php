<?php
session_start();

$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

?>
<div id="admin_left_div">

	<div class="ui-widget ui-corner-all block">
		<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
			Actions
		</div>
		<div class="blockContent">
			<ul>
				<li>
					<a href="#" onClick="generateTmp(); return false;">
						Recalculer TMP
					</a>
				</li>
				<?php if (isset($_SESSION[DROIT_LOG_AS])) { ?>
				<li>
					<a href="#" onClick="logAsList(); return false;">
						Se connecter sur un autre compte
					</a>
				</li>
				<?php } ?>
				
				<li>
					<a href="#" onClick="manageLog(); return false;">
						Gérer les logs
					</a>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
</div>

<div id="editDiv">

</div>

<script>

function generateTmp() {
	showLoadingPopup();
	$.ajax({
		type: 'POST',
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : "action=generateTmp"
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	hideLoadingPopup();
}


function logAsList() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'adminActionLogAs.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#editDiv').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}

function manageLog() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'adminActionLogManagement.php', 
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#editDiv').html(data);
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
?>