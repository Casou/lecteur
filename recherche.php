<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."entete.php";

$allDanses = MetierDanse::getAllowedDanse();
$allEvenements = MetierEvenement::getAllAllowedEvenement();
$allProfesseurs = MetierProfesseur::getAllAllowedProfesseur();
$allTags = MetierTag::getAllTag();

?>

<div id="title" class="recherche_screen">
	<h1>Recherche</h1>
</div>

<main id="recherche_screen">
	<div id="recherche" class="ui-widget ui-widget-content ui-corner-all block">
		<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
			Critères de recherche
		</div>
		<div class="ui-widget-content ui-corner-bottom blockContent blockContentRecherche">
			<form id="rechercheForm" action="rechercheResultat.php" method="post" onSubmit="return false;">
			<table style="width : 100%;">
				<tr>
					<td colspan="3" id="tousLesCriteres">
						Rechercher selon
						<select name="operatorCriteres">
							<option value="all">Tous les critères</option>
							<option value="one">Au moins un critère</option>
						</select>
					</td>
					<th>
						<div title="Coefficient d'importance. Plus ce coefficient est grand, plus le critère est pris en compte dans la pertinance"
							class="cursorQuestion">
							Coeff
						</div>
					</th>
				</tr>
				<tr>
					<th>
						<label for="rechercheVideo">Nom de la vidéo</label>
					</th>
					<td colspan="2"> 
						<input id="rechercheVideo" type="text" name="nom_affiche" size="70" />
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_nom_affiche" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				
				<tr>
					<th>Type(s)</th>
					<td id="rechercheType" colspan="2">
						<input type="checkbox" name="tousTypes" value="0" id="tousTypes" onClick="checkAll(this, 'rechercheType', 'type[]');" />
						<label for="tousTypes">-Tous-</label>
						
						<?php foreach($VIDEO_TYPES as $videoType => $label) { ?>
						<input type="checkbox" name="type[]" value="<?= $videoType ?>" id="<?= $videoType ?>" class="checkType" />
						<label for="<?= $videoType ?>"><?= $label ?></label>
						<?php } ?>
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_type" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				
				<tr>
					<th>
						<label>Danse(s)</label>
					</th>
					<td id="rechercheDanse" colspan="2">
						<input type="checkbox" name="tousDanses" value="0" id="tousDanses" onClick="checkAll(this, 'rechercheDanse', 'danse[]');" />
						<label for="tousDanses">-Tous-</label>
						
						<?php foreach($allDanses as $danse) { ?>
						<input type="checkbox" name="danse[]" value="<?= $danse->id ?>" id="danse_<?= $danse->id ?>" />
						<label for="danse_<?= $danse->id ?>"><?= $danse->nom ?></label>
						<?php } ?>
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_danse" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				
				<tr>
					<th>
						<label for="rechercheEvenementAutocomplete">Evènement(s)</label>
					</th>
					<td>
						<input type="text" id="rechercheEvenementAutocomplete" name="evenementInput" onKeyPress="cancelEntry(event);" />
					</td>
					<td>
						<div id="linkedEvents">
						
						</div>
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_evenement" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="recherchePasse">Nom de passe</label>
					</th>
					<td colspan="2"> 
						<input id="recherchePasse" type="text" name="passe" size="70" />
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_passe" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				<tr>
					<th>
						<label>Niveau(x)</label>
					</th>
					<td id="rechercheNiveaux" colspan="2">
						<input type="checkbox" name="tousNiveaux" value="0" id="tousNiveaux" onClick="checkAll(this, 'rechercheNiveaux', 'niveau[]');" />
						<label for="tousNiveaux">-Tous-</label>
						
						<?php foreach($NIVEAUX as $niveau => $libelle) { ?>
							<input type="checkbox" name="niveau[]" value="<?= $niveau ?>" id="<?= $niveau ?>" />
							<label for="<?= $niveau ?>"><?= $libelle ?></label>
					<?php } ?>
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_niveau" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="rechercheProfAutocomplete">Professeur(s)</label>
					</th>
					<td>
						<input id="rechercheProfAutocomplete" type="text" name="professeurInput" />
					</td>
					<td>
						<div id="linkedProfs">
						
						</div>
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_professeur" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				
				
				<tr>
					<th>
						<label for="rechercheTagAutocomplete">Tags</label>
					</th>
					<td style="width : 150px;">
						<input type="text" id="rechercheTagAutocomplete" name="tagInput" />
					</td>
					<td>
						<div id="linkedTags">
						
						</div>
					</td>
					<td style="text-align : center;">
						<input type="text" name="coeff_tag" size="1" value="1" class="coeff" style="text-align : right;" />
					</td>
				</tr>
				
				
				<tr>
					<th>
						
					</th>
					<td style="width : 150px;" colspan="3">
						<input type="checkbox" id="only_no_passes_check" name="only_no_passes" />
						<label for="only_no_passes_check">Uniquement les vidéos sans passes</label>
					</td>
				</tr>
			</table>
			</form>
			
			<button id="vider" style="margin-top : 10px; float : right;">Vider</button>
			<button id="rechercher" style="margin-top : 10px;">Rechercher</button>
		</div>
	</div>
	
	<div id="resultat">
	
	</div>
</main>

<script>


function cleanForm() {
	if (!confirm('Voulez-vous effacer tous les critères de recherche ?')) {
		return false;
	}
	$('#rechercheVideo').val('');
	$('#recherchePasse').val('');
	$('#rechercheTag').val('');
	$('.coeff').val('1');
	$('form input[type=checkbox]').each(function() {
		$(this)[0].checked = false;
		$(this).button("refresh");
	});
	$("#linkedTags").html('');
	$("#linkedEvents").html('');
	$("#linkedProfs").html('');
}

function checkAll(checkbox, tdId, checkBoxName) {
	checkboxes = document.getElementsByName(checkBoxName);
  	for(var i in checkboxes) {
		checkboxes[i].checked = checkbox.checked;
  	}

  	$('#' + tdId + ' input[type=checkbox]').button("refresh");
}


function addTagOnEntry(event, input) {
	if (event.keyCode == 13) {
		addTag(input.value);
		event.preventDefault();
		return false;
	}
}

function addTag(label) {
	if (label == "") {
		return;
	}
	$("#linkedTags").append('<div class="tag">'
			+ '<input type="hidden" name="tag[]" value="' + escapeHtml(label) + '" style="display : none;" />'
			+ label + ' <a href="#" onClick="deleteTag(this); return false;">'
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');
	$("#tagAutocomplete").val('');
}


function addEvenementVideo(id, label) {
	$("#linkedEvents").append('<div id="evenement_' + id + '">' + 
			'<input type="hidden" name="evenement[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteEvenement(' + id + '); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');
	$("#rechercheEvenementAutocomplete").val('');
}


function addProfesseurVideo(id, label) {
	$("#linkedProfs").append('<div id="prof_' + id + '">' + 
			'<input type="hidden" name="professeur[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteProfesseur(' + id + '); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');

	$("#rechercheProfAutocomplete").val('');
}

function addTagVideo(id, label) {
	$("#linkedTags").append('<div id="tag_' + id + '" class="tag">' + 
			'<input type="hidden" name="tag[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteTag(' + id + '); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');

	$("#rechercheTagAutocomplete").val('');
}



function deleteEvenement(id) {
	$('#evenement_' + id).fadeOut('slow', function() {
		$('#evenement_' + id).remove();
	});
	
}

function deleteTag(id) {
	$('#tag_' + id).fadeOut('slow', function() {
		$('#tag_' + id).remove();
	});
}

function deleteProfesseur(id) {
	$('#prof_' + id).fadeOut('slow', function() {
		$('#prof_' + id).remove();
	});
}


function research() {
	showLoadingPopup();
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'rechercheResultat.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'html',
		data: {
			formulaire: $('#rechercheForm').serialize() // Les donnees que l'on souhaite envoyer au serveur au format JSON
		},
		success: function(data, textStatus, jqXHR) {
			$('#resultat').html(data);
			hideLoadingPopup();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
		}
	});
}







$(document).ready(function() {

	var evenements = [
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
		} ?> 
	];


	var professeurs = [
		<?php
		$isFirst = true; 
		foreach($allProfesseurs as $prof) {
			if(!$isFirst) {
				echo ", ";
			}
			?>
			{
				 value: "<?= $prof->id ?>",
				 label: "<?= $prof->nom ?>"
			}
			<?php
			$isFirst = false;
		} 
		?> 
	];

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

				
	var accentMap = {
		"á": "a","à":"a","â":"a","ä":"a",
		"é": "e","è":"e","ê":"e","ë":"e",
		"í": "i","ì":"i","î":"i","ï":"i",
		"ó": "o","ò":"o","ô":"o","ö":"o",
		"ú": "u","ù":"u","û":"u","ü":"u",
		"Á": "A","À":"A","Â":"A","Ä":"A",
		"É": "E","È":"E","Ê":"E","Ë":"E",
		"Í": "I","Ì":"I","Î":"I","Ï":"I",
		"Ó": "O","Ò":"O","Ô":"O","Ö":"O",
		"Ú": "U","Ù":"U","Û":"U","Ü":"U"
	};
	
	var normalize = function( term ) {
		var ret = "";
		for ( var i = 0; i < term.length; i++ ) {
			ret += accentMap[ term.charAt(i) ] || term.charAt(i);
		}
		return ret;
	};

	$( "#rechercheEvenementAutocomplete" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( evenements, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		addEvenementVideo(ui.item.value, ui.item.label);
			return false;
		},
		focus: function(event, ui) { 
			event.preventDefault(); 
			$(event.target).val(ui.item.label);
		}
	 });

	$( "#rechercheProfAutocomplete" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( professeurs, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		addProfesseurVideo(ui.item.value, ui.item.label);
			return false;
		},
		focus: function(event, ui) { 
			event.preventDefault(); 
			$(event.target).val(ui.item.label);
		}
	 });

	$( "#rechercheTagAutocomplete" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( tags, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		addTagVideo(ui.item.value, ui.item.label);
			return false;
		},
		focus: function(event, ui) { 
			event.preventDefault(); 
			$(event.target).val(ui.item.label);
		}
	 });

	$("#rechercher").button({
		icons: {
			secondary: "ui-icon-search"
		}
	}).click(function() {
		research();
	});

	$("#vider").button({
		icons: {
			secondary: "ui-icon-trash"
		}
	}).click(function() {
		cleanForm();
	});

	$("#rechercheType").buttonset();
	$("#rechercheDanse").buttonset();
	$("#rechercheNiveaux").buttonset();

});

$("#recherche").keypress(function(e) {
    if(e.which == 13) {
    	research();
    }
});


</script>


<?php
include_once $pathToPhpRoot."pied.php";
?>
