<?php

include_once $pathToPhpRoot.'popupAllowUser.php';
include_once $pathToPhpRoot.'popupAllowProfile.php';
include_once $pathToPhpRoot.'popupPlaylist.php';
include_once $pathToPhpRoot.'popupAttachTag.php';
 
$showAction = false;
foreach($DROIT_ACTION as $droit) {
	if(isset($_SESSION[$droit])) {
		$showAction = true;
		break;
	}
}

if($showAction) { ?>
	<select class="action_select" >
		<option value="no_action">Action...</option>
		<?php if(isset($_SESSION[DROIT_ACTION_ALLOW_USER])) { ?>
		<option value="popupUser" >Affecter à un utilisateur</option>
		<?php } ?>
		<?php if(isset($_SESSION[DROIT_ACTION_ALLOW_PROFILE])) { ?>
		<option value="popupProfile">Affecter à un profil</option>
		<?php } ?>
		<?php if(isset($_SESSION[DROIT_ACTION_USE_PLAYLIST])) { ?>
		<option value="popupPlaylist">Ajouter à une playlist</option>
		<?php } ?>
		<?php if(isset($_SESSION[DROIT_ACTION_AFFECT_TAG])) { ?>
		<option value="popupTag">Affecter un tag</option>
		<?php } ?>
	</select>
<?php } ?>

<script type="text/javascript">
	function masterCheckbox(id) {
		$('#' + id + ' .check_video').prop('checked', $('#' + id + ' .masterCheckbox').prop('checked')); 
	}

	function popupUser() {
		openAllowUserDialog($('.check_video:checked'));
	}

	function popupProfile() {
		openAllowProfileDialog($('.check_video:checked'));
	}

	function popupPlaylist() {
		openPlaylistDialog($('.check_video:checked'));
	}

	function popupTag() {
		openTagVideoDialog($('.check_video:checked'));
	}
	
	$(document).ready(function() {
		$(".action_select").change(function() {
			var action = $(this).val();
			if (action == "popupUser") {
		    	popupUser();
			} else if (action == "popupProfile") {
		    	popupProfile();
		    } else if (action == "popupPlaylist") {
		    	popupPlaylist();
		    } else if (action == "popupTag") {
		    	popupTag();
		    } else if (action != "no_action") {
		        alert('Action inconnue : ' + action);
		    }
			$(this).val("no_action");
		});
	});

</script>