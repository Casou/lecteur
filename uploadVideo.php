<?php
$pathToPhpRoot = './';
include_once $pathToPhpRoot."includes.php";

if (!move_uploaded_file($_FILES['file']['tmp_name'], PATH_RAW_FILE.DIRECTORY_SEPARATOR.formatFileName($_FILES['file']['name']))) {
	header('HTTP/1.1 500 Server error');
	echo "VOUS ... NE PASSEREZ ... PAAAAAAAAAAS !!!!!";
} else {
	header('HTTP/1.1 200 OK');
	// header('HTTP/1.1 500 Server error');
	echo 'Vidéo "'.$_FILES['file']['name'].'" uploadée';
}

?>