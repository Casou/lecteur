<?php

include_once $pathToPhpRoot.'popupProf.php';
include_once $pathToPhpRoot.'popupDanse.php';
include_once $pathToPhpRoot.'popupEvenement.php';
include_once $pathToPhpRoot.'popupTag.php';
include_once $pathToPhpRoot.'popupTheme.php';

?>

<div id="menu" class="menu_visible">
	<img src="style/images/barre_menu.png" id="menu_bar" />
	<h1>
		<?= $_SESSION["user"] ?>
	</h1>
	
	<ul id="menu_list">
		<li class="level_1" id="menu_accueil_li">
			<a href="index.php" id="menu_accueil">
				<span class="menu_item_img">
					<img src="style/images/home.png" />
				</span>
				<span class="menu_item_text">
					Accueil
				</span>
			</a>
		</li>
	<?php if (isset($_SESSION[DROIT_CONSULT])) { ?>
		<li class="level_1 selected">
			<a href="#" onClick="return false;">
				Rechercher
			</a>
		
			<ul class="menu_list_level_2">
				<li class="level_2">
					<a href="recherche.php">
						<span class="menu_item_img">
							<img src="style/images/search.png" />
						</span>
						<span class="menu_item_text">
							Recherche vidéo
						</span>
					</a>
				</li>
				<li class="level_2">
					<a href="listeEvenements.php">
						Danse / Évènement
					</a>
				</li>
				<li class="level_2">
					<a href="listeNiveaux.php">
						Danse / Niveau
					</a>
				</li>
				<li class="level_2">
					<a href="listeProfesseurs.php">
						Danse / Prof
					</a>
				</li>
			<?php if (isset($_SESSION[DROIT_CAN_BOOKMARK])) { ?>
				<li class="level_2">
					<a href="listeFavoris.php">
						<span class="menu_item_img">
							<img src="style/images/favori.png" />
						</span>
						<span class="menu_item_text">
							Favoris
						</span>
					</a>
				</li>
			<?php } ?>
			</ul>
		</li>
		
		
		<?php if(isset($_SESSION[DROIT_ACTION_USE_PLAYLIST])) { ?>
		<li class="level_1">
			<a href="playlist.php">
				Playlists
			</a>
		</li>
		<?php } ?>
		
	<?php } ?>
	
	
	<li class="level_1">
		<a href="#" onClick="return false;">
			Administrer
		</a>
		<ul class="menu_list_level_2">
			<li class="level_2">
				<a href="#" onClick="showPopup('themeDialog'); return false;">
					<span class="menu_item_img">
							<img src="style/images/themes.png" />
						</span>
					<span class="menu_item_text">
						Changer de thème
					</span>
				</a>
			</li>
			<?php if (isset($_SESSION[DROIT_ADMIN]) || $_SESSION["user"] == "admin") { ?>
				<li class="level_2">
					<a href="admin.php">
						<span class="menu_item_img">
							<img src="style/images/param_mini.png" />
						</span>
						<span class="menu_item_text">
							Administration
						</span>
					</a>
				</li>
				
				<li class="level_2">
					<a href="news.php">
						<span class="menu_item_text">
							Gérer les news
						</span>
					</a>
				</li>
			<?php } ?>
		
			<?php if (isset($_SESSION[DROIT_EDIT_VIDEO])) { ?>
				<li class="level_2">
					<a href="#" onClick="showPopup('danseDialog'); return false;">
						Gérer les danses
					</a>
				</li>
				<li class="level_2">
					<a href="#" onClick="showPopup('profDialog'); return false;">
						Gérer les profs
					</a>
				</li>
				<li class="level_2">
					<a href="#" onClick="showPopup('evtDialog'); return false;">
						Gérer les évènements
					</a>
				</li>
				<li class="level_2">
					<a href="#" onClick="showPopup('tagDialog'); return false;">
						Gérer les tags
					</a>
				</li>
			<?php } ?>
		</ul>
	</li>
	
	
			
		
		<?php if (isset($_SESSION[DROIT_UPLOAD]) || isset($_SESSION[DROIT_EDIT_VIDEO])) { ?>
		<li class="level_1">
			<a href="#" onClick="return false;">
				Gestion vidéos
			</a>
			<ul class="menu_list_level_2">
		<?php } ?>
			<?php if (isset($_SESSION[DROIT_UPLOAD])) { ?>
			
				<!-- 
				<li class="level_2">
					<a href="upload.php">
						Upload
					</a>
				</li>
				-->
				<li class="level_2">
					<a href="manageRawVideos.php">
						Vidéos brutes
					</a>
				</li>
				<li class="level_2">
					<a href="manageVideosBin.php">
						Corbeille
					</a>
				</li>
				<?php if (isset($_SESSION[DROIT_ADMIN]) || $_SESSION["user"] == "admin") { ?>
				<li class="level_2">
					<a href="export.php">
						Exporter
					</a>
				</li>
				<?php } ?>
			<?php } ?>
		
			<?php if (isset($_SESSION[DROIT_EDIT_VIDEO])) { ?>
				<li class="level_2 videoDispo">
					<a href="manageVideos.php">
						<span class="menu_item_img">
							<img src="style/images/video.png" />
						</span>
						<span class="menu_item_text">
							Vidéos dispo
						</span>
					</a>
				</li>
			<?php } ?>
			
		<?php if (isset($_SESSION[DROIT_UPLOAD]) || isset($_SESSION[DROIT_EDIT_VIDEO])) { ?>
			</ul>
		</li>
		<?php } ?>
		
		
		
		<?php if ($_SESSION["userLogged"] != $_SESSION["userId"]) { ?>
		<li class="level_1">
			<a href="#" onClick="logAs(<?= $_SESSION["userLogged"] ?>); return false;">
				Retour profil
			</a>
		</li>
		<?php } ?>
		
		<li class="level_1 deconnexion red_button">
			<a href="login.php?action=disconnect">
				Déconnexion
			</a>
		</li>
	</ul>
</div>

<!-- 
<div id="collapse_menu">
	<a href="#" onClick="collapseMenu(); return false;"></a>
</div>
-->

<script>
	function showPopup(id) {
		$('#' + id).dialog("open");
	}


	/*
	function collapseMenu() {
		var width = $('#menu').css('width');
		if ($('#menu').hasClass('menu_visible')) {
			$( "#menu, #menu_bar, #collapse_menu, #bodyContent" ).animate({
				'margin-left' : '-=' + width
			}, 1000, function() {
				$('#menu').removeClass('menu_visible');
				$('#collapse_menu').addClass('collapsed');
			});
			
		} else {
			$( "#menu, #menu_bar, #collapse_menu, #bodyContent" ).animate({
				'margin-left' : '+=' + width
			}, 1000, function() {
				$('#menu').addClass('menu_visible');
				$('#collapse_menu').removeClass('collapsed');
			});
		}
	}
	*/

	$(document).ready(function() {
		$("li.deconnexion a").button();
	});
</script>