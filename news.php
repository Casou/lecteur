<?php
$pathToPhpRoot = './';
include_once "entete.php";

$all_news = MetierNews::getAllNews();

?>

<div id="news_admin">
</div>

<div id="news_edit" class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Editer une news
	</div>
	<div id="news_edit_content" class="ui-widget-content">
		<form action="#" onSubmit="return false;">
			<input type="hidden" id="id_news" name="id" />
		
			<div id="date_debut_div">
				<label for="date_debut">Date de début</label>
				<input id="date_debut" name="date_debut" size="10" readonly="readonly" />
			</div>
			
			<div id="date_fin_div">
				<label for="date_fin">Date de fin</label>
				<input id="date_fin" name="date_fin" size="10" readonly="readonly" />
			</div>
			
			<div id="texte_div">
				<input type="hidden" id="texte_hidden" name="texte" />
				<textarea id="texte_news" rows="25" cols="80"></textarea>
			</div>
			
			<div id="save_div">
				<button id="save">Enregistrer</button>
			</div>
		</form>
	</div>
	
</div>



<script>
	
function retrieveNews() {
	$.ajax({
		type: 'POST', 
		url: 'news_tableau.php',
		dataType : 'html',
		async : false,
		success: function(data, textStatus, jqXHR) {
			$('#news_admin').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
			throw new Exception("Exception reçue lors du save");
		}
	});
}












$(document).ready(function() {
	

    tinymce.init({
        selector: "#texte_news",
        content_css : "<?= APPLICATION_ABSOLUTE_URL ?>js/tinymce/styleTinyMCE.css",
        removed_menuitems: "newdocument",
        menubar: false,
        plugins: [
			"link preview hr code"
		],
		toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect",
		toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink | code | removeformat",

	        
    });

    $('#save').button({
		icons: {
			primary: "ui-icon-disk"
		}
	}).click(function() {
		saveNews();
	});

    retrieveNews();
});



</script>


<?php
include_once "pied.php";
?>
	
