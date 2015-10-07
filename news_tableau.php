<?php
$pathToPhpRoot = './';
include_once "includes.php";

$all_news = MetierNews::getAllNews();

?>

<div class="content">
	<table class="videoEvenement listeResultats" id="all_news">
		<thead>
			<tr>
				<th class="id">Id</th>
				<th class="texte">Texte</th>
				<th class="date_creation">Date création</th>
				<th class="date_debut">Date début</th>
				<th class="date_fin">Date fin</th>
				<th class="actions"> </th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($all_news as $news) { ?>
			<tr>
				<td class="id"><?= $news->id ?></td>
				<td class="texte"><?= strip_tags($news->texte) ?></td>
				<td class="date_creation"><?= Fwk::reformateDate($news->date_creation, 'Y-m-d', 'd/m/Y') ?></td>
				<td class="date_debut"><?= Fwk::reformateDate($news->date_debut, 'Y-m-d', 'd/m/Y') ?></td>
				<td class="date_fin"><?= Fwk::reformateDate($news->date_fin, 'Y-m-d', 'd/m/Y') ?></td>
				<td class="actions">
					<a href="#" onClick="editNews(<?= $news->id ?>); return false;">
						<img src="<?= APPLICATION_ABSOLUTE_URL ?>style/images/modify_mini.png" />
					</a>
					<a href="#" onClick="deleteNews(<?= $news->id ?>); return false;">
						<img src="<?= APPLICATION_ABSOLUTE_URL ?>style/images/delete_cross.png" />
					</a>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>


<script>
	
function addNews() {
	$('#id_news').val('');
	$('#date_debut').val('');
	$('#date_fin').val('');
	tinymce.activeEditor.setContent('');
	$('#news_edit').show();
}

function editNews(id_news) {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : 'action=getNews&id=' + id_news
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
				hideLoadingPopup();
				throw new Exception("Statut reçu lors du save n'est pas 'OK'");
			} else {
				var news = data.infos['news'];
				$('#id_news').val(news.id);
				
				$('#date_debut').val(formatDate(news.date_debut));
				$('#date_fin').val(formatDate(news.date_fin));
				tinymce.activeEditor.setContent(news.texte);
				$('#news_edit').show();

				retrieveNews();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
			throw new Exception("Exception reçue lors du save");
		}
	});
	hideLoadingPopup();
}



function saveNews() {
	if (!checkForm()) {
		return;
	}
	
	showLoadingPopup();
	$('#texte_hidden').val(tinymce.activeEditor.getContent());
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : 'action=saveNews&' + $('#news_edit_content form').serialize()
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
				hideLoadingPopup();
				throw new Exception("Statut reçu lors du save n'est pas 'OK'");
			} else {
				retrieveNews();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
			throw new Exception("Exception reçue lors du save");
		}
	});
	hideLoadingPopup();
}

function checkForm() {
	if ($('#date_debut').val() == '' || $('#date_fin').val() == '') {
		alert('Veuillez renseigner les dates de début et de fin');
		return false;
	}

	return true;
}


function deleteNews(id_news) {
	if (!confirm('Voulez-vous supprimer complètement cette news ?')) {
		return;
	}


	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageAdminController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : 'action=deleteNews&id=' + id_news
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
				hideLoadingPopup();
				throw new Exception("Statut reçu lors du save n'est pas 'OK'");
			} else {
				$('#news_edit').hide();
				retrieveNews();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
			throw new Exception("Exception reçue lors du save");
		}
	});
	hideLoadingPopup();
}












$(document).ready(function() {
	$('#all_news').dataTable( {
		"bJQueryUI": true,
		"oLanguage": {
			"sLengthMenu": "Afficher _MENU_ enregistrements par page",
			"sZeroRecords": "Aucun enregistrement",
			"sInfo": "Enregistrement n°_START_ à _END_ / _TOTAL_",
			"sInfoEmpty": "Pas d'enregistrement à afficher",
			"sInfoFiltered": "(filtré sur _MAX_ enregistrements)"
		},
		"iDisplayLength": <?= VIDEO_PAGINATION_DEFAULT ?>,
		"aLengthMenu": [
						 [<?= VIDEO_PAGINATION_NB ?>],
						 [<?= VIDEO_PAGINATION_STRING ?>]
					],
		"aoColumns": [
			{ "bSortable": false },
			{ "bSortable": false },
				null,
				null,
				null,
			{ "bSortable": false }
		],
		"sPaginationType": "full_numbers",
		"aaSorting": [[ 4, "desc" ]],
	});

	$('#date_debut, #date_fin').datepicker({
		dateFormat: 'dd/mm/yy'
	});

	$('#all_news_wrapper .ui-widget-header.ui-corner-tl').append('<button id="add_news">Ajouter une news</button>');
	$('#add_news').button({
			icons: {
				primary: "ui-icon-plus"
			}
	}).click(function() {
		addNews();
	});

});



</script>
	
