<?php
include_once "entete.php";
include "playerDialog.php";

?>


<script>

$(document).ready(function() {
	alert("load");
	$(this).load('ressources/video_traitees/6_Rock_debutant.MOV.webm.srt', 
			function (responseText, textStatus, req) {
					console.log(responseText);
    		});
});
</script>

<?php 
include_once "pied.php";
?>