<?php
$evenements = MetierEvenement::getAllEvenement();
$couleurs = MetierAccordeonCouleur::getAllAccordeonCouleur();
?>

<div id="evtDialog" style="display : none" title="Gérer les évènements">
	<form action="#" onSubmit="return false;">
		<input type="hidden" name="action" value="addEvenement" />
		
		Nom de l'évènement : 
		<input type="text" name="nom" class="nomInput" maxlength="50" />
		Date : 
		<input type="text" name="date" class="dateInput" size="11" />
		Ville : 
		<input type="text" name="ville" class="villeInput" maxlength="50" />
		
		<select name="couleur">
			<?php foreach($couleurs as $couleur) { ?>
			<option value="<?= $couleur->id ?>"><?= $couleur->libelle ?></option>
			<?php } ?>
		</select>
		<button>Ajouter</button>
		
	</form>
	
	<h2>Liste des évènements</h2>
	<table id="tableEvts" class="manageTablePopup">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Date</th>
				<th>Ville</th>
				<th>Couleur</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($evenements as $evt) { ?>
			<tr id="evenement_<?= $evt->id ?>">
				<td>
					<input id="inputNom_<?= $evt->id ?>" maxlength="50"
						size="40"
						type="text" class="noBorder" value='<?= escapeSimpleQuoteHTML($evt->nom) ?>'
						onFocus="$(this).removeClass('noBorder');"
						onBlur="$(this).addClass('noBorder');" 
						onChange="changeEvenement(<?= $evt->id ?>);" />
				</td>
				<td>
					<input id="inputDate_<?= $evt->id ?>" size="11"
						type="text" class="noBorder dateInput" value="<?= formatDateToDisplay($evt->date) ?>"
						onFocus="$(this).removeClass('noBorder');"
						onBlur="$(this).addClass('noBorder');" 
						onChange="changeEvenement(<?= $evt->id ?>);" />
				</td>
				<td>
					<input id="inputVille_<?= $evt->id ?>" maxlength="50"
						type="text" class="noBorder" value="<?= $evt->ville ?>"
						onFocus="$(this).removeClass('noBorder');"
						onBlur="$(this).addClass('noBorder');" 
						onChange="changeEvenement(<?= $evt->id ?>);" />
				</td>
				<td>
					<select id="selectCouleur_<?= $evt->id ?>" onChange="changeEvenement(<?= $evt->id ?>);">
						<?php foreach($couleurs as $couleur) { 
							$selected = "";
							if ($evt->couleur == $couleur->id) {
								$selected = "selected=\"selected\"";
							}
						?>
							<option value="<?= $couleur->id ?>" <?= $selected ?>><?= $couleur->libelle ?></option>
						<?php } ?>
					</select>
				</td>
				<td>
					<a id="inputAction_<?= $evt->id ?>" href="#" onClick="removeEvenement(<?= $evt->id ?>); return false;">
						<img src="style/images/delete.png" />
					</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>

<script>
	function addEvenement() {
		
		if (!confirm('Voulez-vous ajouter cet évènement ?')) { 
			return false;
		}
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : $('#evtDialog form').serialize()
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var evenement = data.infos['evenement'];

					var selected = "";
					var select = '<select id="selectCouleur_' + evenement.id + '" onChange="changeEvenement(' + evenement.id + ');">';
				  	<?php foreach($couleurs as $couleur) { ?>
					  	selected = "";
					  	if (evenement.couleur == <?= $couleur->id ?>) {
					  		selected = "selected=\"selected\"";
					  	}
					  	select += '<option value="<?= $couleur->id ?>" ' + selected + '><?= $couleur->libelle ?></option>';
					<?php } ?>
					select += '</select>';
		  			
					var ai = $('#tableEvts').dataTable().fnAddData( [
					  '<input id="inputNom_' + evenement.id + '" maxlength="50" size="40" '  + 
								'type="text" class="noBorder" value="' + evenement.nom + '" ' + 
								'onFocus="$(this).removeClass(\'noBorder\');" ' + 
								'onBlur="$(this).addClass(\'noBorder\');" '  + 
								'onChange="changeEvenement(' + evenement.id + ');" /> ',
					  '<input id="inputDate_' + evenement.id + '" size="11" ' + 
								'type="text" class="noBorder dateInput" value="' + evenement.date+ '"' + 
								'onFocus="$(this).removeClass(\'noBorder\');"' + 
								'onBlur="$(this).addClass(\'noBorder\');" ' + 
								'onChange="changeEvenement(' + evenement.id + ');" />',
					  '<input id="inputVille_' + evenement.id + '" maxlength="50" ' +  
								'type="text" class="noBorder" value="' + evenement.ville + '"' + 
								'onFocus="$(this).removeClass(\'noBorder\');"' + 
								'onBlur="$(this).addClass(\'noBorder\');" ' + 
								'onChange="changeEvenement(' + evenement.id + ');" />',
					  select,
					  '<a id="inputAction_' + evenement.id + '" href="#" onClick="removeEvenement(' + evenement.id + '); return false;">' + 
								'<img src="style/images/delete.png" />' + 
					  '</a>' ]
					);
					var n = $('#tableEvts').dataTable().fnSettings().aoData[ ai[0] ].nTr;
					$(n).attr('id', 'evenement_' + evenement.id);

					$('#evtDialog form input[name=nom]').val('');
					$('#evtDialog form input[name=date]').val('');
					$('#evtDialog form input[name=ville]').val('');
				}
				updateEvenements();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function changeEvenement(id) {		
		var nom = $('#inputNom_' + id).val();
		var date = $('#inputDate_' + id).val();
		var ville = $('#inputVille_' + id).val();
		var couleur = $('#selectCouleur_' + id).val();
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=changeEvenement&id=" + id + "&nom=" + encodeURI(nom) + 
					"&date=" + encodeURI(date) + "&ville=" + encodeURI(ville) + "&couleur=" + couleur
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					// Si tout se passe bien, on ne fait rien
				}
				updateEvenements();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	function removeEvenement(id) {
		if (!confirm('Voulez-vous supprimer cet évènement ?')) { 
			return false;
		}
		
		$.ajax({
			type: 'POST', 
			url: 'ajaxController/manageController.php',
			dataType : 'json',
			data: {
				formulaire : "action=removeEvenement&id=" + id
			},
			success: function(data, textStatus, jqXHR) {
				if (data.status != 'OK') {
					alert('[' + data.status + '] ' + data.message);
				} else {
					var index = $("#tableEvts").dataTable().fnGetPosition($("#evenement_" + id)[0]);
					$('#evenement_' + id).hide('slow', function() {
						$("#tableEvts").dataTable().fnDeleteRow( index );
					});
				}
				updateEvenements();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert("Une erreur est survenue : \n" + jqXHR.responseText);
			}
		});
	}

	jQuery.fn.dataTableExt.oSort['fr_date-asc']  = function(a,b) {
		var dateA = a.substring(a.indexOf('value=') + 7);
		var dateA = dateA.substring(0, dateA.indexOf('"'));

		var dateB = b.substring(b.indexOf('value=') + 7);
		var dateB = dateB.substring(0, dateB.indexOf('"'));
		
		var frDatea = dateA.split('/');
		var frDateb = dateB.split('/');

		var x = (frDatea[2] + frDatea[1] + frDatea[0]) * 1;
		var y = (frDateb[2] + frDateb[1] + frDateb[0]) * 1;

		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};

	jQuery.fn.dataTableExt.oSort['fr_date-desc'] = function(a,b) {
		var dateA = a.substring(a.indexOf('value=') + 7);
		var dateA = dateA.substring(0, dateA.indexOf('"'));

		var dateB = b.substring(b.indexOf('value=') + 7);
		var dateB = dateB.substring(0, dateB.indexOf('"'));
		
		var frDatea = dateA.split('/');
		var frDateb = dateB.split('/');

		var x = (frDatea[2] + frDatea[1] + frDatea[0]) * 1;
		var y = (frDateb[2] + frDateb[1] + frDateb[0]) * 1;

		return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
	};

	$(document).ready(function() {
		$('#evtDialog').dialog({
			autoOpen: false,
			modal: true,
			width : 770,
			height : 500,
			resizable : false
		});

		$("#evtDialog button").button({
			icons: {
				primary: "ui-icon-circle-plus"
			}
		}).click(function() {
			addEvenement();
		});

		$( ".dateInput" ).datepicker({
			"dateFormat" : 'dd/mm/yy',
			"firstDay" : 1
		});


		$('#tableEvts').dataTable( {
			 "bJQueryUI": true,
			 "oLanguage": {
				"sLengthMenu": "Afficher _MENU_ enregistrements par page",
				"sZeroRecords": "Aucun enregistrement",
				"sInfo": "Enregistrement n°_START_ à _END_ / _TOTAL_",
				"sInfoEmpty": "Pas d'enregistrement à afficher",
				"sInfoFiltered": "(filtré sur _MAX_ enregistrements)"
			},
			"aLengthMenu": [
							 [10],
							 [10]
						],
			"iDisplayLength": 10,
			"sPaginationType": "full_numbers",
			"aoColumns": [
				null,
				{"sType": "fr_date"},
				null,
				{ "bSortable": false },
				{ "bSortable": false }
			],
			"aaSorting": [[ 1, "desc" ]] // on trie par date descendante
		});
		
	});

</script>