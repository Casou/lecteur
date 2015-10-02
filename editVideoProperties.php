<?php
$pathToPhpRoot = './';
include_once "entete.php";

$id = $_GET['id'];

$video = MetierVideo::getVideoById($id);
$isFavori = MetierVideo::isFavori($video->id);

$allDanses = MetierDanse::getAllDanse(true);
$dansesVideo = MetierDanse::getDanseIdByVideo($id);

$allEvenements = MetierEvenement::getAllEvenement();
$evenementVideo = MetierEvenement::getEvenementByVideo($id, true);

$allTags = MetierTag::getAllTag();
$tagsVideo = MetierTag::getTagByVideo($id);

$allPlaylists = MetierPlaylist::getAllPlaylist();
$playlistsVideo = MetierPlaylist::getPlaylistForVideo($id);

$passesVideo = MetierPasse::getPasseByVideo($id);

$allProfesseurs = MetierProfesseur::getAllProfesseur();
$professeursVideo = MetierProfesseur::getProfesseurByVideo($id);

$nextId = MetierVideo::getNextVideoId($id);
$previousId = MetierVideo::getPreviousVideoId($id);

$profilesSelect = MetierProfil::getAllProfil();
$profilAllowed = MetierProfil::getProfilesAllowedForVideo(array($id), true);
$usersSelect = MetierUser::getAllUser();
$usersAllowed = MetierUser::getUserAllowedForVideo(array($id), true);
?>

<div id="message_front"></div>

<div id="title">
	<h1>Modification des propriétés : <?= $video->nom_video ?></h1>
</div>

<div id="playerDiv">
	<div id="player">Chargement de la vidéo...</div>
	<!-- <video id="player" title="Prévisualisation" width="640" height="360" controls>
		<source src="<?= changeBackToSlash(PATH_CONVERTED_FILE)."/".escapeDoubleQuote($video->nom_video) ?>" type="video/webm" />
	</video>-->
</div>

<form action="#" onSubmit="return false;" id="videoForm">
	<input type="hidden" name="action" value="saveVideoProperties" />
	<input type="hidden" name="id" value="<?= $video->id ?>" id="id_video" />

	<table id="editVideoProperties" style="margin : 10px auto auto auto;">
		<?php if (isset($_SESSION[DROIT_CAN_BOOKMARK])) { ?>
		<tr>
			<th colspan="4">
				<a href="#" onClick="changeFavori(<?= $video->id ?>); return false;" style="color : black;">
				<?php if($isFavori) { ?>
					<img src="style/images/favori.png" title="Retirer des favoris"
						class="favori_<?= $video->id ?>" />
				<?php } else { ?>
					<img src="style/images/favori_off.png" title="Ajouter des favoris"
						class="favori_<?= $video->id ?>" />
				<?php } ?>
					Favori
				</a>
			</th>
		</tr>
		<?php } ?>
		<tr>
			<th>Nom du fichier <span class="required">*</span></th>
			<td colspan="3">
				<?= $video->nom_video ?>
				<input type="hidden" name="nom_video" value="<?= $video->nom_video ?>" />
			</td>
		</tr>
		<tr>
			<th>Nom affiché <span class="required">*</span></th>
			<td colspan="3">
				<input type="text" name="nom_affiche" value="<?= htmlspecialchars($video->nom_affiche) ?>" size="100"
					maxlength="255" onKeyPress="cancelEntry(event);" />
			</td>
		</tr>
		<tr>
			<th>Type <span class="required">*</span></th>
			<td id="radioTypeVideo" colspan="3">
				<?php foreach($VIDEO_TYPES as $videoType => $label) { 
					$checked = '';
					if ($video->type == $videoType) {
						$checked = ' checked="checked" ';
					}
				?>
				<input type="radio" name="type" value="<?= $videoType ?>" id="<?= $videoType ?>" <?= $checked ?> onChange="checkProfileAffected();" />
				<label for="<?= $videoType ?>"><?= $label ?></label>
				<?php } ?>
			</td>
		</tr>
		
		
		<tr>
			<th>
				Danse(s) <span class="required">*</span>
			</th>
			<td id="editVideoPropertiesCheckDanse" colspan="3">
				<?php foreach($allDanses as $danse) { 
					$checked = '';
					if (in_array($danse->id, $dansesVideo)) {
						$checked = ' checked="checked" ';
					}
				?>
				<input type="checkbox" name="danse[]" value="<?= $danse->id ?>" id="danse_<?= $danse->id ?>" <?= $checked ?> onChange="checkProfileAffected();" />
				<label for="danse_<?= $danse->id ?>"><?= $danse->nom ?></label>
				<?php } ?>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="evenementAutocomplete">Evènement</label>
			</th>
			<td style="width : 150px;" colspan="2">
				<?php if ($evenementVideo == null) { ?>
				<input type="text" id="evenementAutocomplete" name="evenementInput" onKeyPress="cancelEntry(event);" size="50" />
				<div id="linkedEvents"></div>
				<?php } else {
						$label = "$evenementVideo->date - $evenementVideo->nom - $evenementVideo->ville";
				?>
				<input type="text" id="evenementAutocomplete" name="evenementInput" onKeyPress="cancelEntry(event);" size="50" style="display : none;" />
				<div id="linkedEvents">
					<div id="evenement_<?= $evenementVideo->id ?>">
						<input type="hidden" name="id_evenement" value="<?= $evenementVideo->id ?>" />
						<?= $label ?>
						<a href="#" onClick="deleteEvenement(<?= $evenementVideo->id ?>); return false;">
							<img src="style/images/delete_cross.png" />
						</a>
					</div>
				</div>
				<?php } ?>
			</td>
			<td rowspan="4">
				<select class="visible_by_select" id="visible_by_profile" name="profils[]" multiple="multiple" onClick="popupEditVideoProfilUser();">
					<optgroup label="Visibles par (profils)">
						<?php foreach ($profilAllowed as $profilDto) { 
							$profil = $profilDto->profil; 
						?>
						<option value="<?= $profil->id ?>"><?= $profil->nom ?></option>
						<?php } ?>
					</optgroup>
				</select>
				<select class="visible_by_select" id="visible_by_user" name="users[]" multiple="multiple" onClick="popupEditVideoProfilUser();">
					<optgroup label="Visibles par (utilisateurs)">
						<?php foreach ($usersAllowed as $userDto) { 
							$user = $userDto->user;
						?>
						<option value="<?= $user->id ?>"><?= $user->login ?></option>
						<?php } ?>
					</optgroup>
				</select>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="professeurAutocomplete">Professeur(s)</label>
			</th>
			<td style="width : 150px;">
				<input type="text" id="professeurAutocomplete" name="professeurInput" onKeyPress="cancelEntry(event);" />
			</td>
			<td>
				<div id="linkedProfs">
					<?php foreach($professeursVideo as $prof) { 
					?>
						<div id="prof_<?= $prof->id ?>">
							<input type="hidden" name="professeur[]" value="<?= $prof->id ?>" />
							<?= $prof->nom ?>
							<a href="#" onClick="deleteProfesseur(<?= $prof->id ?>); return false;">
								<img src="style/images/delete_cross.png" />
							</a>
						</div>
					<?php } ?>
				</div>
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="tagAutocomplete">Tags</label>
			</th>
			<td style="width : 150px;">
				<input type="text" id="tagAutocomplete" name="tagInput" onKeyPress="cancelEntry(event);" maxlength="50" />
			</td>
			<td>
				<div id="linkedTags">
				<?php foreach($tagsVideo as $tag) {	?>
					<div id="tag_<?= $tag->id ?>" class="tag">
						<input type="hidden" name="tag[]" value="<?= $tag->id ?>" style="display : none;" />
						<?= $tag->label ?> 
						<a href="#" onClick="deleteTag(<?= $tag->id ?>); return false;">
							<img src="style/images/delete_cross.png" />
						</a>
					</div>
				<?php } ?>
				</div>
			</td>
		</tr>
		
		
		<tr>
			<th>
				<label for="playlistAutocomplete">Playlists</label>
			</th>
			<td style="width : 150px;">
				<input type="text" id="playlistAutocomplete" name="playlistInput" onKeyPress="cancelEntry(event);" maxlength="50" />
			</td>
			<td>
				<div id="linkedPlaylists">
				<?php foreach($playlistsVideo as $playlist) {	?>
					<div id="playlist_<?= $playlist->id ?>" class="playlist">
						<input type="hidden" name="playlist[]" value="<?= $playlist->id ?>" style="display : none;" />
						<?= $playlist->nom ?> 
						<a href="#" onClick="deletePlaylist(<?= $playlist->id ?>); return false;">
							<img src="style/images/delete_cross.png" />
						</a>
					</div>
				<?php } ?>
				</div>
			</td>
		</tr>
		
		
		<tr>
			<th>
				<label for="passesAutocomplete">Passes</label>
			</th>
			<td style="width : 150px;">
				<input type="text" id="passesAutocomplete" name="passeInput" 
					onKeyPress="addPasseOnEntry(event, this);" maxlength="255"  />
				<select id="passesNiveau">
				<?php foreach($NIVEAUX as $niveau => $libelle) { ?>
					<option value="<?= $niveau ?>"><?= $libelle ?></option>
				<?php } ?>
				</select>
				
				<br/>
				
				<button id="timerInTag">Placer le champ au timer courant</button>
				<input type="text" name="timerIn" id="timerIn" value="00:00:00" />
				<button id="timerInGo">Placer le curseur du lecteur à ce moment</button>
				
				<button id="timerOutTag">Placer le champ au timer courant</button>
				<input type="text" name="timerOut" id="timerOut" value="00:00:00" />
				<button id="timerOutGo">Placer le curseur du lecteur à ce moment</button>
				
				<br/>
				
				<button id="passesAdd">Ajouter</button>
			</td>
			<td colspan="2">
				<div id="linkedPasses">
				<?php foreach($passesVideo as $passe) { ?>
					<div class="passe">
						<input type="hidden" class="passe_libelle"  name="passe[]" value="<?= htmlspecialchars($passe->nom) ?>" />
						<input type="hidden" class="passe_niveau"  name="niveau[]" value="<?= htmlspecialchars($passe->niveau) ?>" />
						<input type="hidden" class="passe_debut" name="timer_debut[]" value="<?= $passe->timer_debut ?>" />
						<input type="hidden" class="passe_fin" name="timer_fin[]" value="<?= $passe->timer_fin ?>" />
						
						<?= htmlspecialchars($passe->nom)." - ".htmlspecialchars($NIVEAUX[$passe->niveau]) ?>
						
						<?php if ($passe->timer_debut != null && $passe->timer_fin != null) { ?>
							[<a href="#" class="playerGoto" onClick="playerGoto('<?= $passe->timer_debut ?>'); return false;"><?= $passe->timer_debut ?></a> - 
							<a href="#" class="playerGoto" onClick="playerGoto('<?= $passe->timer_fin ?>'); return false;"><?= $passe->timer_fin ?></a>]
						<?php } ?>
						
						<a href="#" onClick="editPasse(this); return false;">
							<img src="style/images/modify_mini.png" />
						</a>
						<a href="#" onClick="deletePasse(this); return false;">
							<img src="style/images/delete_cross.png" />
						</a>
					</div>
				<?php } ?>
				</div>
			</td>
		</tr>
	
	</table>
	
	<div style="margin : 10px 0 40px 0; font-style : italic;">
		<span class="required">*</span> Ces paramètres sont obligatoires.
	</div>

	<div>
		<button id="quit" style="float : right;">Quitter</button>
		
		<?php if ($previousId != null) { ?>
		<button id="saveAndPrevious">Sauvegarder et précédente</button>
		<?php } ?>
		<button id="saveAndQuit">Sauvegarder et quitter</button>
		<?php if ($nextId != null) { ?>
		<button id="saveAndNext">Sauvegarder et suivante</button>
		<?php } ?>
	</div>
</form>


<?php include_once $pathToPhpRoot.'popupEditVideoProfileUser.php'; ?>











<script type="text/javascript">

var duree_video_seconds = <?= $video->duree ?>;

function save($quit) {
	showLoadingPopup();
	$('.visible_by_select option').each(function() {
		$(this).attr('selected', true);
	});
	$('#videoForm input[name=action]').val('saveVideoProperties');
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php',
		dataType : 'json',
		async : false,
		data: {
			formulaire : $('#videoForm').serialize()
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
				hideLoadingPopup();
				throw new Exception("Statut reçu lors du save n'est pas 'OK'");
			} else {
				if ($quit) {
					// redirect('manageVideos#video_' + $('#id_video').val());
					if (!window.close()) {
						$('#message_front').html('Configuration Firefox : about:config' +
							'<br />dom.allow_scripts_to_close_windows = true').show();
					}
				}
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			hideLoadingPopup();
			throw new Exception("Exception reçue lors du save");
		}
	});

	$('.visible_by_select option').each(function() {
		$(this).attr('selected', false);
	});
}

function saveAndNext(id) {
	if (id != null) {
		try {
			save(false);
			redirect('editVideoProperties.php?id=' + id);
		} catch (error) { }
	} else {
		save(true);
	}
}

function saveAndPrevious(id) {
	if (id != null) {
		try {
			save(false);
			redirect('editVideoProperties.php?id=' + id);
		} catch (error) { }
	} else {
		save(true);
	}
}

/*
function addTagOnEntry(event, input) {
	if (event.keyCode == 13) {
		addTag(input.value);
		event.preventDefault();
		return false;
	}
}
*/

function addPasseOnEntry(event) {
	if (event.keyCode == 13) {
		addPasse();
		event.preventDefault();
		return false;
	}
}

function addEvenementVideo(id, label) {
	$("#linkedEvents").append('<div id="evenement_' + id + '">' + 
			'<input type="hidden" name="id_evenement" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteEvenement(' + id + '); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');
	$("#evenementAutocomplete").val('');
	$("#evenementAutocomplete").hide();

	checkProfileAffected();
}

function addTagVideo(id, label) {
	if (label == "") {
		return;
	}
	$("#linkedTags").append('<div id="tag_' + id + '" class="tag">'
			+ '<input type="hidden" name="tag[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteTag(' + id + '); return false;">'
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');
	$("#tagAutocomplete").val('');

	checkProfileAffected();
}

function addPlaylistVideo(id, label) {
	if (label == "") {
		return;
	}
	$("#linkedPlaylists").append('<div id="playlist_' + id + '" class="playlist">'
			+ '<input type="hidden" name="playlist[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deletePlaylist(' + id + '); return false;">'
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');
	$("#playlistAutocomplete").val('');
}

function addProfesseurVideo(id, label) {
	$("#linkedProfs").append('<div id="prof_' + id + '">' + 
			'<input type="hidden" name="professeur[]" value="' + id + '" />'
			+ label + ' <a href="#" onClick="deleteProfesseur(' + id + '); return false;">' 
			+ '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');

	$("#professeurAutocomplete").val('');
}

function checkTimer(timer) {
	if (trim(timer) == "") {
		return true;
	}
	
	// regular expression to match required time format 
	re = /^\d{2}:\d{2}:\d{2}$/; 
	if(!timer.match(re)) { 
		return false;
	}

	return true;
}

function addPasse() {
	var label = $("#passesAutocomplete").val();
	if (trim(label) == "") {
		alert('Le nom de la passe ne doit pas être vide');
		return false;
	}
	
	var niveau_id = $("#passesNiveau").val();
	var niveau_label = $("#passesNiveau option[value='" + niveau_id + "']").text();
	var timer_in = $("#timerIn").val();
	var timer_out = $("#timerOut").val();

	if (timer_in == "00:00:00" && timer_out == "00:00:00") {
		timer_in = "";
		timer_out = "";
	} 

	var linkTimer = "";
	if (!(timer_in == "" && timer_out == "")) {
		if ((timer_in == "" && timer_out != "") || (timer_in != "" && timer_out == "")) {
			alert("Il faut remplir soit les 2 timers soit aucun des 2.");
			return false;
		}
		
		if (!checkTimer(timer_in)) {
			alert("Le format du timer de début est invalid : " + timer_in + " (format 00:00:00)");
			return false;
		}
	
		if (!checkTimer(timer_out)) {
			alert("Le format du timer de fin est invalid : " + timer_out + " (format 00:00:00)");
			return false;
		}
	
		if (timer_in > timer_out) {
			alert("Le timer de début doit être avant le timer de fin");
			return false;
		}

		var timer_in_seconds = toSeconds(timer_in);
		var timer_out_seconds = toSeconds(timer_out);
		if (timer_in_seconds > duree_video_seconds || timer_out_seconds > duree_video_seconds) {
			alert("Les timers ne peuvent pas excéder la durée totale de la vidéo.");
			return false;
		}

		linkTimer = ' [<a href="#" class="playerGoto" onClick="playerGoto(\'' + timer_in + '\'); return false;">' + timer_in + "</a> - "
		+ ' <a href="#" class="playerGoto" onClick="playerGoto(\'' + timer_out + '\'); return false;">' + timer_out + "</a>]";
	}


	$("#linkedPasses").append('<div class="passe">'
			+ '<input type="hidden" class="passe_libelle" name="passe[]" value="' + escapeHtml(label) + '" />'
			+ '<input type="hidden" class="passe_niveau" name="niveau[]" value="' + niveau_id + '" />'
			+ '<input type="hidden" class="passe_debut" name="timer_debut[]" value="' + timer_in + '" />'
			+ '<input type="hidden" class="passe_fin" name="timer_fin[]" value="' + timer_out + '" />'
			+ label + " - " + niveau_label
			+ linkTimer  
			+ ' <a href="#" onClick="editPasse(this); return false;"><img src="style/images/modify_mini.png" /></a>'
			+ ' <a href="#" onClick="deletePasse(this); return false;">' + '<img src="style/images/delete_cross.png" /></a>'
			+ '</div>');
	
	$("#passesAutocomplete").val('');
	$("#timerIn").val('00:00:00');
	$("#timerOut").val('00:00:00');
}

function playerGoto(time) {
	var t = toSeconds(time);
	
	// $("#player video")[0].currentTime = t;
	jwplayer("player").seek(t);
	// Coupe et remet les sous-titres pour les recharger (sinon bug en cas de relecture de la vidéo)
	jwplayer("player").setCurrentCaptions(0);
	jwplayer("player").setCurrentCaptions(1);
}

function deleteEvenement(id) {
	if (!confirm('Voulez-vous supprimer cet évènement des propriétés de la vidéo ?')) {
		return false;
	}

	$("#linkedEvents").html('');
	$("#evenementAutocomplete").show();

	checkProfileAffected();

	/*
	$('#evenement_' + id).hide('slow', function() {
		$('#evenement_' + id).remove();
	});
	*/
}

function deleteTag(id) {
	if (!confirm('Voulez-vous supprimer ce tag des propriétés de la vidéo ?')) {
		return false;
	}
	
	$('#tag_' + id).remove();
	checkProfileAffected();
}

function deletePlaylist(id) {
	if (!confirm('Voulez-vous supprimer cette playlist des propriétés de la vidéo ?')) {
		return false;
	}
	
	$('#playlist_' + id).remove();
}


function editPasse(baliseA) {
	var div_parent = $(baliseA).parents('div.passe');
	$('#passesAutocomplete').val(div_parent.find('.passe_libelle').val());
	$('#passesNiveau').val(div_parent.find('.passe_niveau').val());
	$('#timerIn').val(div_parent.find('.passe_debut').val());
	$('#timerOut').val(div_parent.find('.passe_fin').val());
}

function deletePasse(baliseA) {
	if (!confirm('Voulez-vous supprimer cette passe des propriétés de la vidéo ?')) {
		return false;
	}
	
	$(baliseA).parents('div.passe').hide('slow', function() {
		$(baliseA).parents('div.passe').remove();
	});
}

function deleteProfesseur(id) {
	if (!confirm('Voulez-vous supprimer ce professeur des propriétés de la vidéo ?')) {
		return false;
	}
	$('#prof_' + id).hide('slow', function() {
		$('#prof_' + id).remove();
	});
}



function setTimerToCurrent(idTimer) {
	$('#' + idTimer).val(formatForTimer($("#player video")[0].currentTime));
}


function setPlayerToTimer(idTimer) {
	playerGoto($('#' + idTimer).val());
}






var lockCheck = false;
var checkAgain = false;
function checkProfileAffected() {
	if (lockCheck) {
		checkAgain = true;
		return;
	}

	$('.visible_by_select').attr('disabled', true);
	lockCheck = true;
	checkAgain = false;
	$('#videoForm input[name=action]').val('checkProfileAffected');
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php',
		dataType : 'json',
		data: {
			formulaire : $('#videoForm').serialize()
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				var profils = data.infos['profils'];
				var profil = null;
				var profils_avant = $('#visible_by_profile option[disabled=disabled]');
				$('#visible_by_profile option[disabled=disabled]').remove();
				$('#linked_profile_select_edit option[disabled=disabled]').remove();
				
				for (var i = 0; i < profils.length; i++) {
					profil = profils[i];
					$('#visible_by_profile option[value=' + profil.id + ']').remove();
					$('#available_profile_select_edit option[value=' + profil.id + ']').remove();
					$('#linked_profile_select_edit option[value=' + profil.id + ']').remove();
					
					$('#visible_by_profile optgroup').prepend(
							'<option value="' + profil.id + '" disabled="disabled">' +
								profil.nom +
							'</option>');
					$('#linked_profile_select_edit').prepend($('#visible_by_profile option[value=' + profil.id + ']').clone());
				}
				
				
				$(profils_avant).each(function() {
					if (!$('#visible_by_profile option[value=' + $(this).val() + ']').length) {
						$('#available_profile_select_edit').prepend(
							'<option value="' + $(this).val() + '">' +
								$(this).html() +
							'</option>');
					}
				});

				lockCheck = false;
				if (checkAgain) {
					checkProfileAffected();
				} else {
					$('.visible_by_select').attr('disabled', false);
				}
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
			lockCheck = false;
		}
	});
	
}


function popupEditVideoProfilUser() {
	var ids_profile = new Array();
	$('#visible_by_profile option').each(function() {
		ids_profile.push($(this).val());
	});

	var ids_user = new Array();
	$('#visible_by_user option').each(function() {
		ids_user.push($(this).val());
	});

	openEditVideoAllowProfileUserDialog(ids_profile, ids_user);
}







$(document).ready(function() {

	
	jwplayer("player").setup({
		file: encodeURI('<?= APPLICATION_ABSOLUTE_URL.PATH_CONVERTED_FILE."/".escapeDoubleQuote($video->nom_video) ?>'),
		image: encodeURI('<?= APPLICATION_ABSOLUTE_URL.PATH_CONVERTED_FILE."/".escapeDoubleQuote($video->nom_video) ?>.jpg'),
		width : '600px',
		height : '100%',
		autostart: false,
		tracks: [{ 
			file: encodeURI(escapeSpaces('<?= APPLICATION_ABSOLUTE_URL.PATH_CONVERTED_FILE."/".escapeDoubleQuote($video->nom_video) ?>.vtt')), 
			label: "French",
			kind: "captions",
			"default": true 
		}],
		skin: {
			name: "lecteur",
		},
	    captions: {
	        color: '#FFFFFF',
	        fontFamily : 'Tahoma, Arial, serif',
	        backgroundOpacity: 50,
	        edgeStyle : 'dropshadow'
	    }
	});
	var statusBeforeSeek = null;
	jwplayer("player").on('seek', function() {
		statusBeforeSeek = jwplayer("player").getState();
	});
	jwplayer("player").on('seeked', function() {
		if (statusBeforeSeek == 'paused') {
			jwplayer("player").pause(true);
		}
		statusBeforeSeek = null;
	});
	

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

	var playlists = [
				<?php
				$isFirst = true; 
				foreach($allPlaylists as $playlist) {
					if(!$isFirst) {
						echo ", ";
		  			}
		  			?>
					{
						 value: "<?= $playlist->id ?>",
						 label: "<?= $playlist->nom ?>"
					}
					<?php
		  			$isFirst = false;
		  		} 
				?> 
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
	 
	$( "#evenementAutocomplete" ).autocomplete({
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

	$( "#tagAutocomplete" ).autocomplete({
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

	$( "#playlistAutocomplete" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( playlists, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		addPlaylistVideo(ui.item.value, ui.item.label);
			return false;
		},
		focus: function(event, ui) { 
			event.preventDefault(); 
			$(event.target).val(ui.item.label);
		}
	 });

	$( "#professeurAutocomplete" ).autocomplete({
		minLength: 0,
		source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( professeurs, function( value ) {
				value = value.label || value.value || value;
				return matcher.test( value ) || matcher.test( normalize( value ) );
			}) );
		},
	 	select: function( event, ui ) {
	 		event.preventDefault();
	 		addProfesseurVideo(ui.item.value, ui.item.label);
			return false;
		},
		focus: function(event, ui) {
			event.preventDefault();
			$(event.target).val(ui.item.label); 
		}
	 });



	$("#editPropertiesVisualizeButton").button();

	$("#saveAndQuit").button({
		icons: {
			primary: "ui-icon-disk"
		}
	}).click(function() {
		save(true);
	});

	$("#saveAndNext").button({
		icons: {
			primary: "ui-icon-disk",
			secondary: "ui-icon-circle-arrow-e"
		}
	}).click(function() {
		saveAndNext(<?= $nextId?>);
	});

	$("#saveAndPrevious").button({
		icons: {
			primary: "ui-icon-circle-arrow-w",
			secondary: "ui-icon-disk"
			
		}
	}).click(function() {
		saveAndPrevious(<?= $previousId?>);
	});

	
	
	$("#passesAdd").button({
		icons: {
			primary: "ui-icon-circle-plus"
		},
		text: true
	}).click(function() {
		addPasse($("#passesAutocomplete")[0]);
	});

	$("#timerInTag").button({
		icons: {
			primary: "ui-icon-tag"
		},
		text: false
	}).click(function() {
		setTimerToCurrent('timerIn');
	});
	
	$("#timerInGo").button({
		icons: {
			primary: "ui-icon-seek-next"
		},
		text: false
	}).click(function() {
		setPlayerToTimer('timerIn');
	});

	Globalize.culture("fr-FR");
	$( "#timerIn" ).timespinner({
		min: '00:00:00',
		required: true,
		showSeconds: true
	});
	$( "#timerOut" ).timespinner({
		min: '00:00:00',
		required: true,
		showSeconds: true
	});

	

	$("#timerOutTag").button({
		icons: {
			primary: "ui-icon-tag"
		},
		text: false
	}).click(function() {
		setTimerToCurrent('timerOut');
	});
	
	$("#timerOutGo").button({
		icons: {
			primary: "ui-icon-seek-next"
		},
		text: false
	}).click(function() {
		setPlayerToTimer('timerOut');
	});

	// $( "#timerOut" ).timespinner();


	$("#quit").button({
		icons: {
			secondary : "ui-icon-circlesmall-close",
		}
	}).click(function() {
		if (confirm('Voulez-vous quitter sans sauvegarder les modifications ?')) {
			showLoadingPopup(); 
			redirect('manageVideos.php#video_' + $('#id_video').val());
		}
	});

	
	$('.visible_by_select').on('change', function(e) {
		$('.visible_by_select option').each(function() {
			$(this).attr('selected', false);
		});
		e.preventDefault();
	});
	

	$('#radioTypeVideo').buttonset();

	checkProfileAffected();
});

</script>

<?php

include_once $pathToPhpRoot."pied.php";
?>