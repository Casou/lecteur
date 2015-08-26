<?php 

function endsWith($haystack, $needle) {
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function formatePath($filePath) {
	return str_replace("\\", "/", realpath($filePath));
}

function getFileName($filePath) {
	if (indexOf($filePath, "/") === FALSE && indexOf($filePath, "\\") === FALSE) {
		return $filePath;
	}
	$path = formatePath($filePath);
	return substr($path, lastIndexOf($path, "/") + 1);
}

function indexOf($string, $item) {
	return strpos($string, $item);
}

function lastIndexOf($string, $item) {
	return strrpos($string, $item);
}

function getFileNameWithoutExtension($filePath) {
	$fileName = getFileName($filePath);
	if (indexOf($fileName, ".") === FALSE) {
		return $fileName;
	} else {
		return substr($fileName, 0, lastIndexOf($fileName, "."));
	}
}







function transformSrtToVtt($srtFile, $directory) {
	$vttFileName = $directory.getFileNameWithoutExtension($srtFile).'.vtt';
	
	$vttFile = fopen($vttFileName, 'w');
	
	fputs($vttFile, utf8_decode("WEBVTT")."\n");
	fputs($vttFile, "\n");
	
	$lines = file($directory.$srtFile);
	foreach ($lines as $lineNumber => $lineContent) {
		$lineContent = trim($lineContent);
		
		// Si c'est le numéro de la passe, on le saute
		if (is_numeric($lineContent)) {
			continue;
		}
		
		fputs($vttFile, utf8_decode(str_replace(",000", ".000", $lineContent))."\n");
	}
	
	// fputs($vttFile, utf8_decode("")."\n");
	
	fclose($vttFile);
}







$directory = "ressources/video_traitees/";

$myDirectory = opendir($directory) or die('Erreur');
while ($entry = @readdir($myDirectory)) {
	if (endsWith($entry, '.srt')) {
		transformSrtToVtt($entry, $directory);
	}
}
closedir($myDirectory);

?>