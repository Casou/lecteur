<?php
exit;

function endsWith($haystack, $needle) {
	return strtoupper(substr($haystack, -strlen($needle))) == strtoupper($needle);
}

$dir = 'ressources'.DIRECTORY_SEPARATOR.'video_traitees';
if ($handle = opendir($dir)) {
	
	while (false !== ($entry = readdir($handle))) {
		$fileName = utf8_encode($entry);
		
		if($entry != "." && $entry != ".." && endsWith($entry, ".srt.srt")) {
			rename($dir.DIRECTORY_SEPARATOR.$entry, str_replace(".srt.srt", ".srt", $dir.DIRECTORY_SEPARATOR.$entry));
		}
	}
}

?>