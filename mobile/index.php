<?php
include_once "entete.php";

$danses = MetierDanse::getAllowedDanse();
?>
<div id="div_index">
	<select id="select_event" data-native-menu="false" onChange="$.mobile.changePage('listeEvenements.php?id_danse=' + this.value);">
		<option>Lister par évènement</option>
		<?php foreach ($danses as $danse) {?>
		<option value="<?= $danse->id ?>"><?= $danse->nom ?></option>
		<?php } ?>
	</select>
	
	<?php if (isset($_SESSION[DROIT_CAN_BOOKMARK])) { ?>
	<a id="favorites" href="favoris.php" data-role="button" data-icon="star" data-iconpos="right">Favoris</a>
	<?php } ?>
	
	<?php if(isset($_SESSION[DROIT_ACTION_USE_PLAYLIST])) { ?>
	<a id="favorites" href="playlist.php" data-role="button" data-icon="bullets" data-iconpos="right">Playlists</a>
	<?php } ?>
	
	<a id="search" href="recherche.php" data-role="button" data-icon="search" data-iconpos="right">Rechercher</a>
	
	<a id="force" href="javascript:location.replace('../index.php?action=forcePC')" data-role="button" data-icon="action" data-iconpos="right">Version complète pour PC</a>

</div>


<?php
include_once "pied.php";
?>