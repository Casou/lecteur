function redirect(url) {
	$(location).attr('href', url);
}

function cancelEntry(event) {
	if (event.keyCode == 13) {
		event.preventDefault();
		return false;
	}
}

function trim (myString) {
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
} 

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

function lpad(string, width, char) {
	char = char || '0';
	string = string + '';
	return string.length >= width ? string : new Array(width - string.length + 1).join(char) + string;
}



function formatForTimer(seconds) {
	var sec = Math.round(seconds);
	return lpad(parseInt(sec / 3600), 2, '0') + ":" + lpad(parseInt(sec / 60), 2, '0') + ":" + lpad(sec % 60, 2, '0');
}

function formatForPlayer(timer) {
	var time = timer.split(":");
	return time[0] * 3600 + time[1] * 60 + time[2];
}





function escapeHtml(string) { 
	return string
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
}

function escapeSimpleQuote(string) {
	// return string.replace("'", "\\\'");
	return string.replace(/'/g, "\\\'");
}

function escapeSpaces(string) {
	return string.replace(/ /g, "_");
}





function getCapaciteInKo(value) {
	if (value.indexOf("Ko") !== -1) {
		return value.substring(0, value.indexOf("Ko"));
	} else if (value.indexOf("Mo") !== -1) {
		return value.substring(0, value.indexOf("Mo")) * 1000;
	} else if (value.indexOf("Go") !== -1) {
		return value.substring(0, value.indexOf("Go")) * 1000 * 1000;
	} else {
		return 0;
	}
}


function getFileExtention(fileName) {
	return fileName.split('.').pop();
}

function generateRandom(nbCharacteres) {
	if (nbCharacteres == undefined) {
		nbCharacteres = 7; 
	}
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < nbCharacteres; i++ ) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text;
}




function toSeconds(t) {
    var s = 0.0
    if(t) {
      var p = t.split(':');
      for(i=0;i<p.length;i++)
        s = s * 60 + parseFloat(p[i].replace(',', '.'))
    }
    return s;
}














function changeFavori(videoId) {
	var action = "addFavori";
	if(!$('.favori_' + videoId).attr('src').endsWith('_off.png')) {
		action = "removeFavori";
	}

	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php', 
		dataType : 'json',
		data: {
			formulaire : "action=" + action + "&videoId=" + videoId
		},
		async : false,
		success: function(data, textStatus, jqXHR) {
			if (action == "addFavori") {
				$('.favori_' + videoId).attr('src', 'style/images/favori.png')
										.attr('title', 'Retirer des favoris');
			} else {
				$('.favori_' + videoId).attr('src', 'style/images/favori_off.png')
										.attr('title', 'Ajouter aux favoris');
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
	hideLoadingPopup();
}



function updateDanses() {
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php',
		dataType : 'json',
		data: {
			formulaire : "action=getAllDanses"
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				var allDanses = data.infos['danses'];
				updateEditVideoProperties(allDanses);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}

function updateEditVideoProperties(allDanses) {
	if($('#editVideoPropertiesCheckDanse').size() > 0) {
		var html = '';
		for (var i in allDanses) {
			html = html + '<input type="checkbox" name="danse[]" value="' + allDanses[i].id + '" ' +
				'id="danse_' + allDanses[i].id + '" />' + 
				'<label for="danse_' + allDanses[i].id + '">' + allDanses[i].nom + '</label>';
		}
		$('#editVideoPropertiesCheckDanse').html(html);
	}
	
}






function updateEvenements() {
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php',
		dataType : 'json',
		data: {
			formulaire : "action=getAllEvenementsJs"
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				var allEvenements = data.infos['evenements'];
				updateEvenementAutocomplete(allEvenements);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}

function updateEvenementAutocomplete(allEvenements) {
	// alert(evenements);
	if($('#evenementAutocomplete').size() > 0) {
		evenements = new Array();
		for (var i in allEvenements) {
			evenements[i] = {
				value : allEvenements[i].id,
				label : allEvenements[i].date + " - " + allEvenements[i].nom + " - " + allEvenements[i].ville
			};
		}
		// alert(evenements);
		$( "#evenementAutocomplete" ).autocomplete( "option", "source", evenements);
	}
	
}





function updateProfesseurs() {
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php',
		dataType : 'json',
		data: {
			formulaire : "action=getAllProfesseursJs"
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				var allProfesseurs = data.infos['professeurs'];
				updateProfesseurAutocomplete(allProfesseurs);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}

function updateProfesseurAutocomplete(allProfesseurs) {
	if($('#professeurAutocomplete').size() > 0) {
		professeurs = new Array();
		for (var i in allProfesseurs) {
			professeurs[i] = {
				value : allProfesseurs[i].id,
				label : allProfesseurs[i].nom
			};
		}
		$( "#professeurAutocomplete" ).autocomplete( "option", "source", professeurs);
	}
	
}


function updateTags() {
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageController.php',
		dataType : 'json',
		data: {
			formulaire : "action=getAllTagsJs"
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				var allTags = data.infos['tags'];
				updateTagAutocomplete(allTags);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}

function updateTagAutocomplete(allTags) {
	if($('#tagAutocomplete').size() > 0) {
		tags = new Array();
		for (var i in allTags) {
			tags[i] = {
				value : allTags[i].id,
				label : allTags[i].label
			};
		}
		$( "#tagAutocomplete" ).autocomplete( "option", "source", tags);
	}
	
}


function showLoadingPopup() {
	$('#loadingPopup').show();
}

function hideLoadingPopup() {
	$('#loadingPopup').hide();
}





var charMap = {
   "à": "a", "â": "a", "é": "e", "è": "e", "ê": "e", "ë": "e",
   "ï": "i", "î": "i", "ô": "o", "ö": "o", "û": "u", "ù": "u"
};

var normalize = function(str) {
  $.each(charMap, function(chars, normalized) {
    var regex = new RegExp('[' + chars + ']', 'gi');
    str = str.replace(regex, normalized);
  });

  return str;
}







$.widget( "ui.timespinner", $.ui.spinner, {
	options: {
		// seconds
		step: 1000,
		// minutes
		page: 60
	},

	_parse: function( value ) {
		if ( typeof value === "string" ) {
			// already a timestamp
			if ( Number( value ) == value ) {
				return Number( value );
			}
			return +Globalize.parseDate( value );
		}
		return value;
	},

	_format: function( value ) {
		return Globalize.format( new Date(value), "T" );
	}
});