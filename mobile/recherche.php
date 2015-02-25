<?php

$title = "Rechercher";
include_once "entete.php";

$danses = MetierDanse::getAllowedDanse();
$allEvenements = MetierEvenement::getAllAllowedEvenement();
$allProfesseurs = MetierProfesseur::getAllAllowedProfesseur();

?>
<div id="div_recherche">
	<form id="rechercheForm" action="rechercheResultat.php" method="post" onSubmit="return false">
	
		<input type="hidden" name="operatorCriteres" value="all" />
		<input type="hidden" name="coeff_danse" value="1" />
		<input type="hidden" name="coeff_type" value="1" />
		<input type="hidden" name="coeff_evenement" value="1" />
		<input type="hidden" name="coeff_niveau" value="1" />
		<input type="hidden" name="coeff_professeur" value="1" />
		<input type="hidden" name="coeff_nom_affiche" value="1" />
		<input type="hidden" name="coeff_passe" value="1" />
		<input type="hidden" name="coeff_tag" value="1" />
		
	
		<div data-role="fieldcontain">
			<input type="text" name="nom_affiche" id="nom_affiche" placeholder="Nom de la vidéo" />
		</div>	
	
		<select id="select_type" data-native-menu="false" multiple="multiple" name="type[]" >
			<option>Type(s)</option>
			<?php foreach ($VIDEO_TYPES as $videoType => $label) {?>
			<option value="<?= $videoType ?>"><?= $label ?></option>
			<?php } ?>
		</select>
		
		<select id="select_danse" data-native-menu="false" multiple="multiple" name="danse[]">
			<option>Danse(s)</option>
			<?php foreach ($danses as $danse) {?>
			<option value="<?= $danse->id ?>"><?= $danse->nom ?></option>
			<?php } ?>
		</select>
		
		<div id="event_search" data-role="fieldcontain">
			<table>
				<tr>
					<td>
						<ul id="rechercheEvenementAutocomplete" class="autocomplete"	data-role="listview" data-inset="true" data-filter="true" 
								data-filter-placeholder="Rechercher un évènement" data-filter-theme="a"></ul>
						</td>
					<td>
						<div id="linkedEvents">
							<h2>Evènement(s) :</h2>
							<ul id="events_selected">
							</ul>
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		<div data-role="fieldcontain">
			<input id="recherchePasse" type="text" name="passe" placeholder="Nom de la passe" />
		</div>	
	
		<select id="select_niveaux" data-native-menu="false" multiple="multiple" name="niveau[]" >
			<option>Niveau(x)</option>
			<?php foreach ($NIVEAUX as $niveau => $libelle) {?>
			<option value="<?= $niveau ?>"><?= $libelle ?></option>
			<?php } ?>
		</select>
		
		<div id="prof_search" data-role="fieldcontain">
			<table>
				<tr>
					<td>
						<ul id="rechercheProfAutocomplete" class="autocomplete"	data-role="listview" data-inset="true" data-filter="true" 
								data-filter-placeholder="Rechercher un professeur" data-filter-theme="a"></ul>
						</td>
					<td>
						<div id="linkedProfs">
							<h2>Professeur(s) :</h2>
							<ul id="profs_selected">
							</ul>
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		
		<div data-role="fieldcontain">
			<button data-theme="b" onClick="research();">Rechercher</button>
		</div>	
		
	</form>
</div>

<div id="div_recherche_resultat">

</div>

<script type="text/javascript">
<!--
function addEvenementVideo(id, label) {
	$("#linkedEvents").append('<div id="evenement_' + id + '">' + 
					'<input type="hidden" name="evenement[]" value="' + id + '" />'
					+ label + ' <a href="#" onClick="deleteEvenement(' + id + '); return false;">' 
					+ '<img src="style/images/delete_cross.png" /></a>'
					+ '</div>');
	$("#event_search .ui-input-search input").val('');
}

function addProfVideo(id, label) {
	$("#linkedProfs").append('<div id="prof_' + id + '">' + 
			'<input type="hidden" name="professeur[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteProfesseur(' + id + '); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>')
	$("#prof_search .ui-input-search input").val('');
}

function deleteEvenement(id) {
	$('#evenement_' + id).fadeOut('slow', function() {
		$('#evenement_' + id).remove();
	});
	
}

function deleteProfesseur(id) {
	$('#prof_' + id).fadeOut('slow', function() {
		$('#prof_' + id).remove();
	});
}


function research() {
	$.ajax({
		type: 'POST', // Le type de ma requete
		url: 'rechercheResultat.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'html',
		data: {
			formulaire: $('#rechercheForm').serialize() // Les donnees que l'on souhaite envoyer au serveur au format JSON
		},
		success: function(data, textStatus, jqXHR) {
			$('#div_recherche').hide();
			$('#div_recherche_resultat').html(data);
			$('#div_recherche_resultat').show();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}




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
	}?>
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


$(document).ready(function() {
	


});

$(document).on( "pageinit", "#divPage", function() {
	$( "#rechercheEvenementAutocomplete" ).on( "filterablebeforefilter", function ( e, data ) {
		var $ul = $(this);						// $ul refers to the shell unordered list under the input box
		var value = $( data.input ).val();		// this is value of what user entered in input box
		var dropdownContent = "" ;				// we use this value to collect the content of the dropdown
		$ul.html("") ;							// clears value of set the html content of unordered list

		// on third character, trigger the drop-down
		if ( value && value.length > 2 ) {
			$('#rechercheEvenementAutocomplete').show();			 
			$ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading' ></span></div></li>" );
			$ul.listview( "refresh" );
			$.each(evenements, function( index, val ) {
				dropdownContent += "<li value='" + val.value + "'>" + val.label + "</li>";
				$ul.html( dropdownContent );
				$ul.listview( "refresh" );
				$ul.trigger( "updatelayout");	
			});
			
		}
	});

	$( "#rechercheProfAutocomplete" ).on( "filterablebeforefilter", function ( e, data ) {
		var $ul = $(this);						// $ul refers to the shell unordered list under the input box
		var value = $( data.input ).val();		// this is value of what user entered in input box
		var dropdownContent = "" ;				// we use this value to collect the content of the dropdown
		$ul.html("") ;							// clears value of set the html content of unordered list

		// on third character, trigger the drop-down
		if ( value && value.length > 2 ) {
			$('#rechercheProfAutocomplete').show();			 
			$ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading' ></span></div></li>" );
			$ul.listview( "refresh" );
			$.each(professeurs, function( index, val ) {
				dropdownContent += "<li value='" + val.value + "'>" + val.label + "</li>";
				$ul.html( dropdownContent );
				$ul.listview( "refresh" );
				$ul.trigger( "updatelayout");	
			});
			
		}
	});

	
});	
	

// click to select value of auto-complete
$( document).on( "click", "#rechercheEvenementAutocomplete li", function() {
	addEvenementVideo($(this).val(), $(this).html()); 
	$('#rechercheEvenementAutocomplete').hide(); 
});

$( document).on( "click", "#rechercheProfAutocomplete li", function() {
	addProfVideo($(this).val(), $(this).html()); 
	$('#rechercheProfAutocomplete').hide(); 
});


//-->
</script>

<?php
//include "playerDialog.php";

include_once "pied.php";
?>