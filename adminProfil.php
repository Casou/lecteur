<?php
session_start();

$pathToPhpRoot = './';

include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

if (isset($_POST['id']) && $_POST['id'] != null) {
	$id = $_POST['id'];
	$profilDTO = MetierProfil::getProfilById($id);
	$currentProfil = $profilDTO->profil;
} else {
	$profilDTO = new ProfilDTO();
	$currentProfil = new Profil();
}

$danses = MetierDanse::getAllDanse(true);
$allEvenements = MetierEvenement::getAllEvenement();
$allTags = MetierTag::getAllTag();

?>

<div class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		<?php if($currentProfil->id != null) { ?>
			Modification du profil : <?= $currentProfil->nom ?>
		<?php } else { ?>
			Création d'un nouveau profil
		<?php } ?>
	</div>
	<div class="blockContent">
		<form action="#" method="post" id="formProfils" onSubmit="return false;">
			<input type="hidden" id="id" name="id" value="<?= $currentProfil->id ?>" />
			<input type="hidden" id="action" name="action" value="editProfil" />
			
			
			<div>
				<label for="name">Nom : </label>
				<input type="text" name="nom" id="nom" value="<?= $currentProfil->nom ?>" />
				
			<?php 
				$adminChecked = "";
				if ($currentProfil->is_admin) {
					$adminChecked = 'checked="checked"';
				}
			?>
				<input type="checkbox" name="type" id="is_admin" value="on" id="is_admin" <?= $adminChecked ?> onChange="changeAdmin()" />
				<label for="is_admin">Admin</label>

			</div>
			
			<div id="critere_list">
				<h2>Critères : <button id="addCritere">Ajouter</button></h2>
				
			</div>
		</form>
		
		<button id="saveProfil">Sauvegarder</button>
		
		<?php if($currentProfil->id != null) { ?>
		<button id="deleteProfil" style="margin-left : 20px;">Supprimer</button>
		<?php } ?>
	</div>
</div>


<script>

function Critere(danses, types, tags, evenements) {
	this.danses = danses;
	this.types = types;
	this.tags = tags;
	this.evenements = evenements;
}

function addCritere(critere) {

	/*
	var nb = $('.critere').size();
	var new_id = 'critere_' + nb;
	*/
	var new_id = generateRandom();

	var checked;
	var html = ' <div id="' + new_id + '" class="critere"> ' +
		'<div class="critere_innerDiv"> ' +
			'<div class="deleteCritere"> ' +
				'<a href="#" onClick="deleteCritere(\'' + new_id + '\');">' + 
					'<img src="style/images/delete_cross.png" />' +
				'</a>' +
			'</div>' + 
			'<div class="radioButtonSet danses"> ' +
				'Danses :  ';
	
	<?php foreach($danses as $danse) { ?>
		checked = "";
		if (critere.danses.indexOf('<?= $danse->id ?>') >= 0) {
			checked = 'checked="checked"';
		}
		html += '<input type="checkbox" name="danse[]" value="<?= $danse->id ?>" id="danse_<?= $danse->id ?>_' + new_id + '" ' + checked + ' /> ' +
			'<label for="danse_<?= $danse->id ?>_' + new_id + '"><?= $danse->nom ?></label> ';
	<?php } ?>
	
	html += '</div> ' +
			'<div class="radioButtonSet types"> ' +
				'Types de vidéo :  ';
	<?php foreach($VIDEO_TYPES as $videoType => $label) { ?>
		checked = "";
		if (critere.types.indexOf('<?= $videoType ?>') >= 0) {
			checked = 'checked="checked"';
		}
		html += '<input type="checkbox" name="type" value="<?= $videoType ?>" id="<?= $videoType ?>_' + new_id + '" ' + checked + ' /> ' +
		'<label for="<?= $videoType ?>_' + new_id + '"><?= $label ?></label> ';
	<?php } ?>
				
	html += '</div> ';

	html += '<div class="evts_div">' +
		'<label for="evt_' + new_id + '">Evènements : </label>' +
		'<input type="text" name="evtAutocomplete" id="evt_' + new_id + '" />' +
		'<ul id="linked_evt_' + new_id + '"></ul>';
	html += '</div> ';

	html += '<div class="tags_div">' +
		'<label for="tag_' + new_id + '">Tags : </label>' +
		'<input type="text" name="tagAutocomplete" id="tag_' + new_id + '" />' +
		'<div id="linked_tag_' + new_id + '"></div>';
	html += '</div> ';

	html += '<div class="clear"></div> ';

	html +=
		'</div> ' +
	'</div>';
	$('#critere_list').append(html);

	for (var id_tag in critere.tags) {
		addTagCritere(id_tag, critere.tags[id_tag], 'tag_' + new_id);
	};

	for (var id_evt in critere.evenements) {
		addEventCritere(id_evt, critere.evenements[id_evt], 'evt_' + new_id);
	};

	makeEventAutocomplete('evt_' + new_id);
	makeTagAutocomplete('tag_' + new_id);

	$('#' + new_id + ' .radioButtonSet').buttonset();
	
}

function saveProfil() {
	showLoadingPopup();

	var id = $('#id').val();
	var action = $('#action').val();
	var nom = $('#nom').val();
	var is_admin = $('#is_admin').is(':checked');
	var criteres = new Array();
	$('.critere').each(function() {
		var danses = new Array();
		$(this).find('.danses input[type=checkbox]:checked').each(function () {
			danses.push($(this).val());
		});
		var types = new Array();
		$(this).find('.types input[type=checkbox]:checked').each(function () {
			types.push($(this).val());
		});
		var tags = new Array();
		$(this).find('.tag input[type=hidden]').each(function () {
			tags.push($(this).val());
		});
		var events = new Array();
		$(this).find('.event input[type=hidden]').each(function () {
			events.push($(this).val());
		});

		criteres.push(new Critere(danses, types, tags, events));
	});

	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'ajaxController/manageAdminController.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		async : false,
		data: {
			id : id,
			nom : nom,
			is_admin : is_admin,
			formulaire : "action=" + action,
			criteres : criteres
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				adminProfilList();
				adminProfil(data.infos['id']);
			}
			hideLoadingPopup();	
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();	
		}
	});

}


function deleteProfil(idProfil) {
	if (!confirm('Etes-vous sûr de vouloir supprimer ce profil ?')) {
		return false;
	}

	showLoadingPopup();

	$.ajax({
		type: 'POST',
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : "action=deleteProfil&id=" + idProfil
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				adminProfilList();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});

	hideLoadingPopup();
}


function deleteCritere(new_id) {
	if (!confirm('Etes-vous sûr de vouloir supprimer ce critere ?')) {
		return;
	}

	$('#' + new_id).hide(500, function() {
		$(this).remove();
	});
}


function changeAdmin() {
	if ($('#is_admin').is(':checked')) {
		$('#addCritere, div.critere').hide();
	} else {
		$('#addCritere, div.show').show();
	}
}





var tags = [
	<?php
	$isFirst = true; 
	foreach($allTags as $tag) {
		if(!$isFirst) {
			echo ", ";
		}
		?>
		{
			 value: "<?= $tag->id ?>",
			 label: "<?= $tag->label ?>"
		}
		<?php
		$isFirst = false;
	} 
	?> 
];


var events = [
	<?php
	$isFirst = true; 
	foreach($allEvenements as $evenement) {
		if(!$isFirst) {
			echo ", ";
		}
		?>
		{
			 value: "<?= $evenement->id ?>",
			 label: "<?= formatDateToDisplay($evenement->date) ?> - <?= $evenement->nom ?> - <?= $evenement->ville ?>"
		}
		<?php
		$isFirst = false;
	} 
	?> 
];



function makeEventAutocomplete(id_input) {
	$('#' + id_input ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( events, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		addEventCritere(ui.item.value, ui.item.label, id_input);
			return false;
		},
		focus: function(event, ui) { 
			event.preventDefault(); 
			$(event.target).val(ui.item.label);
		}
	 });
}

function makeTagAutocomplete(id_input) {
	$('#' + id_input ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( tags, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		addTagCritere(ui.item.value, ui.item.label, id_input);
			return false;
		},
		focus: function(event, ui) { 
			event.preventDefault(); 
			$(event.target).val(ui.item.label);
		}
	 });
}


function addTagCritere(id, label, id_input) {
	$("#linked_" + id_input).append('<div class="' + id_input + '_' + id + ' tag">' + 
			'<input type="hidden" name="tag[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteElementCritere(' + id + ', \'' + id_input + '\'); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');

	$("#" + id_input).val('');
}

function addEventCritere(id, label, id_input) {
	$("#linked_" + id_input).append('<li class="' + id_input + '_' + id + ' event">' + 
			'<input type="hidden" name="evenement[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteElementCritere(' + id + ', \'' + id_input + '\'); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</li>');

	$("#" + id_input).val('');
}

function deleteElementCritere(id, id_input) {
	$('.' + id_input + '_' + id).fadeOut('slow', function() {
		$('.' + id_input + '_' + id).remove();
	});
}








$(document).ready(function() {
	
	$('#saveProfil').button( {
		icons: {
			secondary: "ui-icon-disk"
		}
	}).click(function() {
		saveProfil();
	});

	$( "#addCritere" ).button({
		icons: {
			primary: "ui-icon-plus"
		},
		text: false
	}).click(function() {
		addCritere(new Critere(new Array(), new Array(), new Array()));
	});

	<?php if($currentProfil->id != null) { ?>
	$('#deleteProfil').button( {
		icons: {
			secondary: "ui-icon-trash"
		}
	}).click(function() {
		deleteProfil(<?= $currentProfil->id ?>);
	});
	<?php } ?>

	var tmp_danses;
	var tmp_types;
	var tmp_tags;
	var tmp_events;
	<?php if (count($profilDTO->criteres) > 0) { 
		foreach($profilDTO->criteres as $critere) {
			$danses_critere = $critere->danses == null ? array() : explode(";", $critere->danses);
			$types_critere = $critere->types_video == null ? array() : explode(";", $critere->types_video);
			$tags_critere = $critere->tags == null ? array() : explode(";", $critere->tags);
			$evenements_critere = $critere->evenements == null ? array() : explode(";", $critere->evenements);
	?>
		tmp_danses = new Array();
		tmp_types = new Array();
		tmp_tags = {};
		tmp_events = {};
	<?php foreach($danses_critere as $danse) {
		if ($danse != null) {
	?>
		tmp_danses.push('<?= $danse ?>');
	<?php } } ?>

	<?php foreach($types_critere as $type) {
		if ($type != null) { 
	?>
		tmp_types.push('<?= $type ?>');
	<?php } } ?>


	<?php 
	foreach($tags_critere as $tag) {
		if ($tag != null) { 
			$tag = MetierTag::getTagById($tag);
			if ($tag != null) {
	?>
		tmp_tags[<?= $tag->id ?>] = '<?= escapeSimpleQuote($tag->label) ?>';
	<?php	} 
		}
	 } ?>

	<?php 
	foreach($evenements_critere as $evenement) {
		if ($evenement != null) {
			$evenement = MetierEvenement::getEvenementById($evenement);
			if ($evenement != null) {
	?>
		tmp_events[<?= $evenement->id ?>] = '<?= $evenement->date." - ".escapeSimpleQuote($evenement->nom." - ".$evenement->ville) ?>';
	<?php	} 
		}
	 } ?>

	 
		addCritere(new Critere(tmp_danses, tmp_types, tmp_tags, tmp_events));
	<?php 
		}
	} ?>


	changeAdmin();
});

</script>
