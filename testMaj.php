<?php
include_once "entete.php";

$url = "http://basile.parent.free.fr/lecteur/ressources/video_traitees/4_M4H00970.MP4.webm";

function retrieve_remote_file_size($url){
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_NOBODY, TRUE);

	$data = curl_exec($ch);
	$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

	curl_close($ch);
	return $size;
}

function remote_file_size($url){
	$head = "";
	$url_p = parse_url($url);

	$host = $url_p["host"];
	if(!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/",$host)){

		$ip=gethostbyname($host);
		if(!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/",$ip)){

			return -1;
		}
	}
	if(isset($url_p["port"]))
		$port = intval($url_p["port"]);
	else
		$port    =    80;

	if(!$port) $port=80;
	$path = $url_p["path"];

	$fp = fsockopen($host, $port, $errno, $errstr, 20);
	if(!$fp) {
		return false;
	} else {
		fputs($fp, "HEAD "  . $url  . " HTTP/1.1\r\n");
		fputs($fp, "HOST: " . $host . "\r\n");
		fputs($fp, "User-Agent: http://www.example.com/my_application\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		$headers = "";
		while (!feof($fp)) {
			$headers .= fgets ($fp, 128);
		}
	}
	fclose ($fp);

	$return = -2;
	$arr_headers = explode("\n", $headers);
	foreach($arr_headers as $header) {

		$s1 = "HTTP/1.1";
		$s2 = "Content-Length: ";
		$s3 = "Location: ";

		if(substr(strtolower ($header), 0, strlen($s1)) == strtolower($s1)) $status = substr($header, strlen($s1));
		if(substr(strtolower ($header), 0, strlen($s2)) == strtolower($s2)) $size   = substr($header, strlen($s2));
		if(substr(strtolower ($header), 0, strlen($s3)) == strtolower($s3)) $newurl = substr($header, strlen($s3));
	}

	if(intval($size) > 0) {
		$return=intval($size);
	} else {
		$return=$status;
	}

	if (intval($status)==302 && strlen($newurl) > 0) {

		$return = remote_file_size($newurl);
	}
	return $return;
}

// $fp = fsockopen ("http://basile.parent.free.fr/lecteur/ressources/video_traitees/4_M4H00970.MP4.webm", 80, $errno, $errstr, 30);  
// $buffer = '';
 
// if (!$fp) {  
//    echo "$errstr ($errno)<br>\n";  
// } else {  
//    fputs($fp, "GET /pictures/francologo.jpg  HTTP/1.0\r\nHost: www.example.comrnrn" );  
// 	$buffer = fread($fp, 512); 
//    fclose($fp);  
 
//    // là tu as les headers http dans $buffer
//    echo $buffer;
//    // tu devrais voir un champ Content-Length à l'écran.
//    // il suffit juste d'extraire la valeur située après le champ ...
// }

//On initialise une session curl
// $ch = curl_init();

// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// //Le Header contiendra les infos dont on a besoin...
// curl_setopt($ch, CURLOPT_HEADER, true);
// //On ne souhaite pas récupérer le contenu de la requête
// //de manière à ne pas alourdir le serveur
// //Sans ce paramètre, le fichier est "téléchargé"
// curl_setopt($ch, CURLOPT_NOBODY, true);
// //On exécute la requête Curl
// curl_exec($ch);

// //$infos contient des informations telles que la taille du fichier
// //son Mime type etc...
// $infos = curl_getinfo($ch);

// print_r($infos);

print remote_file_size($url);
?>


<?php 
include_once "pied.php";
?>