<?php

function endsWith($haystack, $needle) {
	return strtoupper(substr($haystack, -strlen($needle))) == strtoupper($needle);
}


echo "Script désactivé";
exit;


$path = "C:\\Users\\Basile\\Desktop\\Vidéos\\2014.08.10 - Eauze\\DVD 2";
if($dossier = opendir($path)) {
	while(false !== ($fichier = readdir($dossier))) {
		// if($fichier != '.' && $fichier != '..' && $fichier != "ffmpeg-64bits.exe") {
		// echo "$fichier<br/>";
		if(endsWith($fichier, '.VOB')) {
			$file_path = "$path\\$fichier";
			$new_file_path = "$path\\C_$fichier.mp4";
			// echo "$file_path >> $new_file_path";
			$cmd = "\"$path\\ffmpeg-64bits.exe\" -i \"$file_path\" -aq 100 -ac 2 -vcodec libx264 -crf 24 -threads 0 \"$new_file_path\""; 
			// echo "$cmd<br/>";
			passthru($cmd);
		}
	}
	echo "Encodage terminé";
} else {
	echo "Impossible d'ouvrir le dossier";
}





?>