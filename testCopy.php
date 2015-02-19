<?php

header('Content-Type: text/html; charset=utf-8');

// include_once "includes.php";

/*
copy("http://basile.parent.free.fr/lecteur/ressources/video_traitees/474_Niveau_1_-_Olivier_et_Henriette_-_Samedi.VOB.webm.srt", 
	"ressources/474_Niveau_1_-_Olivier_et_Henriette_-_Samedi.VOB.webm.srt");
*/

file_put_contents("ressources/474_Niveau_1_-_Olivier_et_Henriette_-_Samedi.VOB.webm.srt", 
		file_get_contents("http://basile.parent.free.fr/lecteur/ressources/video_traitees/474_Niveau_1_-_Olivier_et_Henriette_-_Samedi.VOB.webm.srt"));

?>