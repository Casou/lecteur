function logAs(id) {
	logAsUrl(id, './');
}


function logAsUrl(id, relativeUrl) {
	showLoadingPopup();
	$.ajax({
		type: 'POST', 
		url: 'ajaxController/manageAdminController.php', // L'url vers laquelle la requete sera envoyee
		dataType : 'json',
		async : false,
		data: {
			formulaire : "action=logAs&id=" + id
		},
		success: function(data, textStatus, jqXHR) {
			if (data.status != 'OK') {
				alert('[' + data.status + '] ' + data.message);
			} else {
				window.location = relativeUrl + "index.php";
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert("Une erreur est survenue : \n" + jqXHR.responseText);
		}
	});
}
