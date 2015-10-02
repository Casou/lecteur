<?php
?>

<div id="themeDialog" style="display : none" title="Changer le thème de l'application">
	<form action="#" onSubmit="return false;">
		<ul>
			<?php 
			foreach($ALL_THEMES as $theme_id => $theme_params) { 
				$selected = $_SESSION['theme'] == $theme_id ? 'checked="checked"' : "";
			?>
			<li>
				<input type="radio" name="theme" value="<?= $theme_id ?>" id="theme_<?= $theme_id ?>" <?= $selected ?> />
				<label for="theme_<?= $theme_id ?>"><?= $theme_params['nom'] ?></label>
			</li>
			<?php } ?>
		</ul>
		
		<button>Changer</button>
	</form>
</div>

<script>
	function changeTheme() {
		var theme = $('#themeDialog input[name=theme]:checked').val();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=changeTheme&theme=" + theme
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					showLoadingPopup();
					location.reload();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	$(document).ready(function() {
		$('#themeDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 400,
			height : 170,
			resizable : false
		});

		$("#themeDialog button").button({
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			if (confirm('Changer le thème du site ?')) {
				changeTheme();
			}
		});
	});

</script>