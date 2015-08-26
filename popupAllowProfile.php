<?php
$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$profilesSelect = MetierProfil::getAllProfil();
?>

<div id="allowProfileDialog" style="display : none" title="Affecter des vidéos à des utilisateurs">
	<h2>Liste des profils</h2>
	<table>
		<tr>
			<td>
				<select id="available_profile_select" multiple="multiple" size="6">
					<?php foreach($profilesSelect as $profileSelect) { ?>
					<option value="<?= $profileSelect->id ?>"><?= $profileSelect->nom ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<a href="#" id="add_allowed_profile"><img src="style/images/fleche_gauche.png" /></a>
				<br/>
				<a href="#" id="remove_allowed_profile"><img src="style/images/fleche_droite.png" /></a>
			</td>
			<td>
				<select id="linked_profile_select"  multiple="multiple" size="6">
				</select>
			</td>
		</tr>
	</table>
	
	<button>Sauvegarder</button>
</div>

<script>
	var id_video_profile;

	function openAllowProfileDialog(ids) {
		showLoadingPopup();
		$('#available_profile_select').append($('#linked_profile_select option'));
		
		var ids_array = new Array();
		$(ids).each(function() {
			ids_array.push($(this).val());
		});

		if (ids_array.length == 0) {
			alert("Veuillez sélectionnner au moins une vidéo;");
			hideLoadingPopup();
			return;
		}

		
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageAllowedVideosController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				action : 'getVideosInfoForProfiles',
				ids : ids_array
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				var profiles = data.infos['profils'];
				for (var i = 0; i < profiles.length; i++) {
					$('#linked_profile_select').append($('#available_profile_select option[value=' + profiles[i] + ']'));
				};
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		id_video_profile = ids_array;
		$('#allowProfileDialog').dialog('open');
		hideLoadingPopup();
	}


	function saveAllowedProfile() {
		showLoadingPopup();
		var id_video_profiles_linked = new Array();
		$('#linked_profile_select option').each(function() {
			id_video_profiles_linked.push($(this)[0].value);
		});
	
		$.ajax({
			type: 'POST', // Le type de ma requete
			url: 'ajaxController/manageAllowedVideosController.php', // L'url vers laquelle la requete sera envoyee
			dataType : 'json',
			data: {
				action : 'saveAllowedVideosForProfiles',
				ids_video : id_video_profile,
				profiles : id_video_profiles_linked
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				$('#allowProfileDialog').dialog('close');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		hideLoadingPopup();
	}


	

	$(document).ready(function() {
		$('#allowProfileDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 260,
			resizable : false,
			close : function(event, ui) {
				$('#allowProfileDialog').dialog( "destroy" );
			}
		});

		$("#allowProfileDialog button").button({
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			saveAllowedProfile();
		});

		$('#add_allowed_profile').click(function() {
			$('#available_profile_select').append($('#linked_profile_select option:selected'));
			return false;
		});

		$('#remove_allowed_profile').click(function() {
			$('#linked_profile_select').append($('#available_profile_select option:selected'));
			return false;
		});
	});

</script>