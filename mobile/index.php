<?php
include_once "entete.php";

$danses = MetierDanse::getAllDanse();
?>
<div id="div_index">
	<select id="select_event" data-native-menu="false" onChange="$.mobile.changePage('listeEvenements.php?id_danse=' + this.value);">
		<option>Lister par évènement</option>
		<?php foreach ($danses as $danse) {?>
		<option value="<?= $danse->id ?>"><?= $danse->nom ?></option>
		<?php } ?>
	</select>
	
	<a id="favorites" href="favoris.php" data-role="button" data-icon="star" data-iconpos="right">Favoris</a>
	
	<a id="search" href="#" data-role="button" data-icon="search" data-iconpos="right" data-theme="b">Rechercher</a>
	
	<a id="force" href="javascript:location.replace('../index.php?action=forcePC')" data-role="button" data-icon="action" data-iconpos="right">Version complète pour PC</a>

</div>


<?php
include_once "pied.php";
?>