<?php

function json_encode_utf8($var) {
	$json = new Services_JSON();
	return $json->encode($var);
}

function endsWith($haystack, $needle) {
	return strtoupper(substr($haystack, -strlen($needle))) == strtoupper($needle);
}

function escapeString($str) {
	return str_replace("'", "''", $str);
}

function escapeSimpleQuote($str) {
	return str_replace("'", "\'", $str);
}
function escapeSimpleQuoteHTML($str) {
	return str_replace("'", "&#39;", $str);
}
function resetSimpleQuote($str) {
	return str_replace("\'", "'" , $str);
}
function escapeDoubleQuote($str) {
	return str_replace("\"", "\\\"", $str);
}
function escapeSpaces($str) {
	return str_replace(" ", "_", $str);
}

function formatNumber($number, $decimals = 2) {
	return number_format($number, $decimals, ',', ' ');
}

function formatFileName($fileName) {
	// return str_replace(" ", "_", stripAccents($fileName));
	return stripAccents($fileName);
}

function formatId($name) {
	return str_replace(" ", "_", stripAccents($name));
}

// function addSlashesToSimpleQuote($str) {
// 	return str_replace("'", "\'", $str);
// }

// function addSlashesToDoubleQuote($str) {
// 	return str_replace('"', '\"', $str);
// }

function changeBackToSlash($str) {
	return str_replace("\\", "/", $str);
}

/*
function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
			'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}
*/

function stripAccents($string) {
	return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}

function formatDateToDisplay($date) {
	$dateArray = date_parse_from_format('Y-m-d', $date);
	return Fwk::lpad($dateArray['day'], 2, '0')."/".
			Fwk::lpad($dateArray['month'], 2, '0')."/".
			$dateArray['year'];
}

function formatDateToMysql($date) {
	$dateArray = date_parse_from_format('d/m/Y', $date);
	return $dateArray['year']."-".
			Fwk::lpad($dateArray['month'], 2, '0')."-".
			Fwk::lpad($dateArray['day'], 2, '0');
	// return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
}

function explode_research_field($value) {
	preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $value, $words);
	$allWords = array();
	foreach($words[0] as $word) {
		if (strrpos($word, '"') !== false) {
			$word = substr($word, 1, strlen($word) - 2);
		}
		$allWords[] = $word;
	}
	
	return $allWords;
}



function generateRandom($nb_chars = 7) {
	$text = "";
	$possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	for($i = 0; $i < $nb_chars; $i++) {
		$text .= substr($possible, floor(rand(0, strlen($possible))), 1);
	}

	return $text;
}






// Pour le tri par date des évènements
function compareEvent($a, $b) {
	$eventA = $a['evenement'];
	$eventB = $b['evenement'];
	
	if ($eventA->date == null) {
		return 1;
	}
	
	if ($eventB->date == null) {
		return -1;
	}

	$dateA = formatDateToMysql($eventA->date);
	$dateB = formatDateToMysql($eventB->date);
	
	if ($dateA == $dateB) {
		return 0;
	}

	return (strcmp($dateA, $dateB));
	// return ($a < $b) ? -1 : 1;
}



function arrayToCommaString($array, $separator = ";") {
	if (!is_array($array)) {
		return $array;
	}
	$string = "";
	foreach($array as $elt) {
		if ($string != "") {
			$string .= $separator;
		}
		$string .= $elt;
	}
	return $string;
}





if( !function_exists('date_parse_from_format') ){
    function date_parse_from_format($format, $date) {
        // reverse engineer date formats
        $keys = array(
            'Y' => array('year', '\d{4}'),              //Année sur 4 chiffres
            'y' => array('year', '\d{2}'),              //Année sur 2 chiffres
            'm' => array('month', '\d{2}'),             //Mois au format numérique, avec zéros initiaux
            'n' => array('month', '\d{1,2}'),           //Mois sans les zéros initiaux
            'M' => array('month', '[A-Z][a-z]{3}'),     //Mois, en trois lettres, en anglais
            'F' => array('month', '[A-Z][a-z]{2,8}'),   //Mois, textuel, version longue; en anglais, comme January ou December
            'd' => array('day', '\d{2}'),               //Jour du mois, sur deux chiffres (avec un zéro initial)
            'j' => array('day', '\d{1,2}'),             //Jour du mois sans les zéros initiaux
            'D' => array('day', '[A-Z][a-z]{2}'),       //Jour de la semaine, en trois lettres (et en anglais)
            'l' => array('day', '[A-Z][a-z]{6,9}'),     //Jour de la semaine, textuel, version longue, en anglais
            'u' => array('hour', '\d{1,6}'),            //Microsecondes
            'h' => array('hour', '\d{2}'),              //Heure, au format 12h, avec les zéros initiaux
            'H' => array('hour', '\d{2}'),              //Heure, au format 24h, avec les zéros initiaux
            'g' => array('hour', '\d{1,2}'),            //Heure, au format 12h, sans les zéros initiaux
            'G' => array('hour', '\d{1,2}'),            //Heure, au format 24h, sans les zéros initiaux
            'i' => array('minute', '\d{2}'),            //Minutes avec les zéros initiaux
            's' => array('second', '\d{2}')             //Secondes, avec zéros initiaux
        );

        // convert format string to regex
        $regex = '';
        $chars = str_split($format);
        foreach ( $chars AS $n => $char ) {
            $lastChar = isset($chars[$n-1]) ? $chars[$n-1] : '';
            $skipCurrent = '\\' == $lastChar;
            if ( !$skipCurrent && isset($keys[$char]) ) {
                $regex .= '(?P<'.$keys[$char][0].'>'.$keys[$char][1].')';
            }
            else if ( '\\' == $char ) {
                $regex .= $char;
            }
            else {
                $regex .= preg_quote($char);
            }
        }

        $dt = array();
        $dt['error_count'] = 0;
        // now try to match it
        if( preg_match('#^'.$regex.'$#', $date, $dt) ){
            foreach ( $dt AS $k => $v ){
                if ( is_int($k) ){
                    unset($dt[$k]);
                }
            }
            if( !checkdate($dt['month'], $dt['day'], $dt['year']) ){
                $dt['error_count'] = 1;
            }
        }
        else {
            $dt['error_count'] = 1;
        }
        $dt['errors'] = array();
        $dt['fraction'] = '';
        $dt['warning_count'] = 0;
        $dt['warnings'] = array();
        $dt['is_localtime'] = 0;
        $dt['zone_type'] = 0;
        $dt['zone'] = 0;
        $dt['is_dst'] = '';
        return $dt;
    }
}

?>
