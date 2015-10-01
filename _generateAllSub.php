<?php 
	$ENTENSION = '.srt';
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="overflow : auto; white-space : nowrap;">

<?php 

function endsWith($haystack, $needle) {
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}


$directory = "ressources/video_traitees/";

$myDirectory = opendir($directory) or die('Erreur');
$ok = 0;
$creation = 0;
while ($entry = @readdir($myDirectory)) {
	$srtFile = $directory.$entry.$ENTENSION;
	if (endsWith($entry, '.webm')) {
		if (!file_exists($srtFile)) {
			touch($srtFile);
			echo "$entry ==> <b>Creation : </b> $srtFile<br/>";
			$creation++;
		} else {
			echo "$entry ==> <b>OK</b><br/>";
			$ok++;
		}
	}
}
closedir($myDirectory);

?>
<p style="font-size : 24px;">
	<?= "$ok OK | $creation créations" ?>
</p>
</body>
</html>