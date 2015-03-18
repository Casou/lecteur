<?php
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init(LOG_FILE_NAME, $pathToPhpRoot);

$tagsSelect = MetierTag::getAllTag();
?>

<div id="attachTagDialog" style="display : none" title="Affecter des tags à des vidéos">
	<h2>Liste des tags</h2>
	<table>
		<tr>
			<td>
				<select id="available_tag_select" multiple="multiple" size="6">
					<?php foreach($tagsSelect as $tag) { ?>
					<option value="<?= $tag->id ?>"><?= $tag->label ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<a href="#" id="add_attach_tag"><img src="style/images/fleche_gauche.png" /></a>
				<br/>
				<a href="#" id="remove_attach_tag"><img src="style/images/fleche_droite.png" /></a>
			</td>
			<td>
				<select id="linked_tag_select"  multiple="multiple" size="6">
				</select>
			</td>
		</tr>	
	</table>
	
	<button>Sauvegarder</button>
</div>

<script>
	var id_video_tag;

	function openTagVideoDialog(ids) {
		showLoadingPopup();
		$('#available_tag_select').append($('#linked_tag_select option'));
		
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
			type: 'POST', 
			url: 'ajaxController/manageAllowedVideosController.php',
			dataType : 'json',
			data: {
				action : 'getTagInfoForVideos',
				ids : ids_array
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				var tags = data.infos['tags'];
				for (var i = 0; i < tags.length; i++) {
					$('#linked_tag_select').append($('#available_tag_select option[value=' + tags[i] + ']'));
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		id_video_tag = ids_array;
		$('#attachTagDialog').dialog('open');
		hideLoadingPopup();
	}


	function saveAttachTag() {
		showLoadingPopup();
		var id_video_tags_linked = new Array();
		$('#linked_tag_select option').each(function() {
			id_video_tags_linked.push($(this)[0].value);
		});
	
		$.ajax({
			type: 'POST',
			url: 'ajaxController/manageAllowedVideosController.php', 
			dataType : 'json',
			data: {
				action : 'saveAttachedTagsForVideos',
				ids_video : id_video_tag,
				tags : id_video_tags_linked
			},
			async : false,
			success: function(data, textStatus, jqXHR) {
				$('#attachTagDialog').dialog('close');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});

		hideLoadingPopup();
	}


	

	$(document).ready(function() {
		$('#attachTagDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 670,
			height : 260,
			resizable : false,
			close : function(event, ui) {
				$('#attachTagDialog').dialog( "destroy" );
			}
		});

		$("#attachTagDialog button").button({
			icons: {
				primary: "ui-icon-disk"
			}
		}).click(function() {
			saveAttachTag();
		});

		$('#add_attach_tag').click(function() {
			$('#available_tag_select').append($('#linked_tag_select option:selected'));
			return false;
		});

		$('#remove_attach_tag').click(function() {
			$('#linked_tag_select').append($('#available_tag_select option:selected'));
			return false;
		});
	});

</script>