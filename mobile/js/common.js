function showLoadingPopup() {
	$('#loadingPopup').show();
}

function hideLoadingPopup() {
	$('#loadingPopup').hide();
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