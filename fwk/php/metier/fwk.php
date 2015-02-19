<?php

class Fwk {
	
	const REQUEST_METHOD_GET = 'GET';
	const REQUEST_METHOD_POST = 'POST';
	const REQUEST_METHOD_PUT = 'PUT';
	const REQUEST_METHOD_DELETE = 'DELETE';
	const REQUEST_ALL_METHODS = 'REQUEST';

	private static $ENCRYPT_SALT = "V!tB4FM@?Uf*N*M5";
	
	
	
	
	public static function execInBackground($path, $exe, $args = "") { 
//	    global $conf; 
	    
	    if (file_exists($path . $exe)) { 
	        chdir($path); 
	        if (substr(php_uname(), 0, 7) == "Windows") { 
	        	$command = "start \"bla\" \"" . $exe . "\" " . escapeshellarg($args);
	        	Logger::debug("execInBackground (Windows) : $command");
	            pclose(popen($command, "r"));    
	        } else {
	        	$command = "./" . $exe . " " . escapeshellarg($args) . " > /dev/null &";
	        	Logger::debug("execInBackground : $command"); 
	            exec($command);    
	        } 
	    } else {
	    	Logger::error("File not exists : ".$path . $exe);
	    	throw new Exception("Fwk::execInBackground => File not exists : ".$path . $exe);
	    }
	} 
	
	/* ******************************************************
	************************** ECHO *************************
	********************************************************* */
	public static function p($string) {
		echo "<p>$string</p>";
	}
	
	public static function h1($string) {
		echo "<h1>$string</h1>";
	}
	
	
	public static function getCallingMethod() {
		$debugTrace = Fwk::getArrayDebugPrintBacktrace();
		
		if (count($debugTrace) <= 2) {
			return null;
		}
		
		$method1 = Fwk::stripMethodName(substr($debugTrace[2], 4, Fwk::indexOf($debugTrace[2], ")") - 3));
		if (count($debugTrace) > 3) {
			$method2 = Fwk::stripMethodName(substr($debugTrace[3], 4, Fwk::indexOf($debugTrace[3], ")") - 3));
			
			return "$method2 / $method1";
		}
		return $method1;
	}
	
	private static function stripMethodName($methodName) {
		if (Fwk::startsWith($methodName, 'include')) {
			return $methodName;
		}
		
		if (Fwk::indexOf($methodName, "::") > 0) {
			return substr($methodName, 0, Fwk::indexOf($methodName, "(") + 1).")";
		}
		
		return $methodName;
	}
	
	public static function getDebugPrintBacktrace($traces_to_ignore = 1) {
		 return implode("\n", Fwk::getArrayDebugPrintBacktrace($traces_to_ignore));
	}
	
	private static function getArrayDebugPrintBacktrace($traces_to_ignore = 1){
	    $traces = debug_backtrace();
	    $ret = array();
	    foreach($traces as $i => $call){
	        if ($i < $traces_to_ignore ) {
	            continue;
	        }
	
	        $object = '';
	        if (isset($call['class'])) {
	            $object = $call['class'].$call['type'];
	            if (is_array($call['args'])) {
	                foreach ($call['args'] as &$arg) {
	                    Fwk::get_arg($arg);
	                }
	            }
	        }        
	
	        $ret[] = '#'.str_pad($i - $traces_to_ignore, 3, ' ')
	        .$object.$call['function'].'('.implode(', ', $call['args'])
	        .') called at ['.$call['file'].':'.$call['line'].']';
	    }
	
	    return $ret;
	}
	
	private static function get_arg(&$arg) {
	    if (is_object($arg)) {
	        $arr = (array)$arg;
	        $args = array();
	        foreach($arr as $key => $value) {
	            if (strpos($key, chr(0)) !== false) {
	                $key = '';    // Private variable found
	            }
	            $args[] =  '['.$key.'] => '.Fwk::get_arg($value);
	        }
	
	        $arg = get_class($arg) . ' Object ('.implode(',', $args).')';
	    }
	}
	
	
	
	/* ******************************************************
	************************ ENCRYPT ************************
	********************************************************* */
	public static function encrypt($string) {
		return sha1(md5(
			substr($string, 0, strlen($string) / 2) 
			.Fwk::$ENCRYPT_SALT 
			.substr($string, strlen($string) / 2, strlen($string))));
	}
	
	
	
	/* ******************************************************
	************************ VERSIONS ***********************
	********************************************************* */

	public static function getApplicationVersion($appName) {
		
		$sql = "SELECT MAX(version) as version
			FROM (
				SELECT *
					FROM ".DoVersion::getTable()."
					WHERE date = (
							SELECT max( date )
							FROM fwk_app_version ) 
						AND application = :appName
			) as maxVersion";
		$param = array("appName" => $appName);
		
		$result = TemplateDAO::getSingleResult($sql, $param);
		return $result['version'];
	}
	
	public static function getAllApplicationVersions($appName) {
		$sql = "SELECT *
				FROM ".DoVersion::getTable()."
				WHERE application = :appName
				ORDER BY date ASC, version ASC";
		$param = array("appName" => $appName);
		return TemplateDAO::getMultipleResult($sql, $param, 'DoVersion');
	}
	
	
	

	
	/* ******************************************************
	************************ STRING *************************
	********************************************************* */
	public static function startsWith($Haystack, $Needle){
	    return strpos($Haystack, $Needle) === 0;
	}
	
	public static function indexOf($string, $item){  
	    return strpos($string, $item);  
	}
	
	public static function lastIndexOf($string, $item){
		return strrpos($string, $item);    
//	    $index = strpos(strrev($string),strrev($item));  
//	    if ($index){  
//	        $index = strlen($string)-strlen($item)-$index;  
//	        return $index;  
//	    } else {  
//			return -1;
//	    }  
	}
	
	public static function convertBooleanToInteger($bool) {
		if ($bool) {
			return 1;
		}
		return 0;
	}
	
	public static function lpad($string, $pad_length, $pad_string = " ") {
		return str_pad($string, $pad_length, $pad_string, STR_PAD_LEFT);
	}
	
	public static function rpad($string, $pad_length, $pad_string = " ") {
		return str_pad($string, $pad_length, $pad_string, STR_PAD_RIGHT);
	}
	
	
	
	
	
	
	/* ******************************************************
	************************* ARRAY *************************
	********************************************************* */
	public static function addArrayToArray($addFrom, $addTo) {
		foreach ($addFrom as $addKey => $addValue) {
			$addTo[$addKey] = $addValue;
		}
	    return $addTo;
	}
	 
	
	
	
	
	/* ******************************************************
	************************* DATES *************************
	********************************************************* */

	public static function testDate($date) {
		return (preg_match('#^([0-9]{2})([/-])([0-9]{2})\2([0-9]{4})$#', $date, $m) == 1 && checkdate($m[3], $m[1], $m[4]));
	}

	public static function parseDate($date, $format = "d/m/Y") {
		return date($format, strtotime($date));
	}
	
	public static function parseDateJourMois($date) {
		return strftime("%d/%m", strtotime($date));
	}
	
	public static function getWeekNumber($dateString) {
		return date('W', strtotime($dateString));
	}
	
	public static function getNextWeek($week, $year, $numberOfWeeks) {
		$allDays = Fwk::getAllDaysOfWeek($week, $year);
		
		$lundi = new DateTime($allDays[0]);
		/*
		//generate all the days
for($i=0;$i<7;$i++)
{
if($day_off_set > 7-$today)
{
$day_off_set = -($today-1);
}
$days[$day_names[$today+$day_off_set]] = date("Y-m-d",time()+(60*60*24*(7+$day_off_set)));
$day_off_set++;
}
*/

		if ($numberOfWeeks > 0) {
			$lundiSemaineSuivante = $lundi->modify("+$numberOfWeeks week");
		} else {
			$lundiSemaineSuivante = $lundi->modify("$numberOfWeeks week");
		}
		return array($lundiSemaineSuivante->format("W"), $lundiSemaineSuivante->format("Y"));
	}
	
	public static function getAllDaysOfWeek($week, $year) {
		if(strftime("%W",mktime(0, 0, 0, 01, 01, $year)) == 1) {
			$mon_mktime = mktime(0, 0, 0, 01, (01 + (($week - 1) * 7)), $year);
		} else {
			$mon_mktime = mktime(0, 0, 0, 01, (01 + (($week) * 7)), $year);
		}
		 
		if(date("w", $mon_mktime) > 1) {
			$decalage = ((date("w", $mon_mktime) - 1) * 60 * 60 * 24);
		}
		
		$lundi = $mon_mktime - $decalage;
		$allDays = array(date("Y-m-d", $lundi));
		for ($i = 1; $i < 7; $i++) {
			$allDays[count($allDays)] = date("Y-m-d", ($lundi + ($i * 60 * 60 * 24)));
		}
	 
		return $allDays;
	}
	

 
	/**
	 * exemple : $dateDo = ("2009-09-07"); $nbrJours = 3 ;
	 * == > datePlus($dateDo,$nbrJours) = "2009-09-10"
	*/
	public static function datePlus($dateDo, $nbrJours) {
		$timeStamp = strtotime($dateDo); 
		$timeStamp += 24 * 60 * 60 * $nbrJours;
		$newDate = date("Y-m-d", $timeStamp);
		return $newDate;
	}
	
	
	public static function formateHeure($nbMinutes) {
		$jours = floor($nbMinutes / 1440);
		 
		$reste = $nbMinutes % 1440;
		$heures = floor($reste/60); 
		
		$minutes = $reste % 60; 
		
		$result = "";
		if ($jours > 0) {
			$result .= $jours.'j ';
		}
		if ($heures > 0) {
			$result .= $heures.'h ';
		}	
		if ($minutes > 0) {
			$result .= $minutes.'mn ';
		}		
		
		return $result;
	}
	
	public static function dateDiff($date1, $date2) {
		return strtotime($date1) - strtotime($date2);
	}
	
	public static function formatDureeEnSecondes($dureeEnSeconde) {
		$dureeFormatee = "";
		$duree = $dureeEnSeconde;
		
		// 3600 * 24
		if ($duree / 86400 > 1) {
			$dureeFormatee .= floor($duree / 86400)."j ";
			$duree = $duree % 86400;
		}
		
		if ($duree / 3600 > 1) {
			$dureeFormatee .= Fwk::lpad(floor($duree / 3600), 2, "0").":";
			$duree = $duree % 3600;
		}
		
		$dureeFormatee .= Fwk::lpad(floor($duree / 60), 2, "0").":";
		$duree = $duree % 60;
		
		$dureeFormatee .= Fwk::lpad($duree, 2, "0");
		
		return $dureeFormatee;
	}
	
	public static function parseDureeEnSecondes($dureeFormatee) {
		$duration_array = explode(':', $dureeFormatee);
		$dureeParsee = $duration_array[0] * 3600 + $duration_array[1] * 60 + round($duration_array[2], 0);
		return $dureeParsee;
	}

	
	
	/* ******************************************************
	************************ REQUEST ************************
	********************************************************* */
	public static function isEmpty($variable) {
		return !isset($variable) || $variable == null || trim($variable) == "";
	}
	
	public static function isNotEmpty($variable) {
		return !Fwk::isEmpty($variable);
	}
	
	public static function redirect($url) {
		if (headers_sent()) {
			echo '<script language="javascript" type="text/javascript">
				<!--
				window.location.replace("'.$url.'");
				-->
				</script>';
			die('Redirection Javascript...');
		} else {
			header( 'Location: ' . $url );
			die('Redirection...');
		}
	}
	
	public static function getGETParametreString($nomParameter, $default="") {
		if (isset($_GET[$nomParameter])) {
			return $_GET[$nomParameter];
		}
		
		return $default;
	}
	
	public static function getPOSTParametreString($nomParameter, $default="") {
		if (isset($_POST[$nomParameter])) {
			return $_POST[$nomParameter];
		}
		
		return $default;
	}
	
	public static function getRequestParameter($method, $nomParameter, $default="") {
		if ($method == Fwk::REQUEST_METHOD_GET && isset($_GET[$nomParameter])) {
			return $_GET[$nomParameter];
		} else if ($method == Fwk::REQUEST_METHOD_POST && isset($_POST[$nomParameter])) {
			return $_POST[$nomParameter];
		} else if (isset($_REQUEST[$nomParameter])) {
			return $_REQUEST[$nomParameter];
		}
		
		return $default;
	}
	
	public static function getRequestCheckParameter($method, $nomParameter, $isCheckedByDefault) {
		if ($method == Fwk::REQUEST_METHOD_GET) {
			if (isset($_POST[$nomParameter]) && !isset($_GET[$nomParameter])) {
				if ($isCheckedByDefault) {
					return "checked=\"checked\"";
				} else {
					return "";
				}
			}
			return $_GET[$nomParameter];
		} else if ($method == Fwk::REQUEST_METHOD_POST) {
			if (isset($_POST[$nomParameter]) && Fwk::isNotEmpty($_POST[$nomParameter])) {
				return "checked=\"checked\"";
			} else {
				if ($isCheckedByDefault) {
					return "checked=\"checked\"";
				}
				return "";
			}
		}
		
		if (!isset($_REQUEST[$nomParameter])) {
			if ($isCheckedByDefault) {
				return "checked=\"checked\"";
			} else {
				return "";
			}
		}
		return $_REQUEST[$nomParameter];
	}
	
	public static function getCheckedParametreString($nomParameter, $isCheckedByDefault) {
		if (!isset($_POST[$nomParameter])) {
			if ($isCheckedByDefault) {
				return "checked=\"checked\"";
			} else {
				return "";
			}
		}
		return $_POST[$nomParameter];
	}
	
	public static function getIp() {
		return $_SERVER['REMOTE_ADDR'];
	}
	
	
	public static function getNavigateur() {
		$navigateur = array("", "", "");
		// [0] Navigateur dans le HTTP_USER_AGENT (ex : "Mozilla")
		// [1] Navigateur décrypté (ex : "Firefox 3")
		// [2] Version
		
		$var_nav = explode(' ',$_SERVER['HTTP_USER_AGENT']);
		$navigateur[0] = $var_nav[0];
		
		if (preg_match("@MSIE 9@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " MSIE 9";
			$navigateur[2] = 9;
		} else if (preg_match("@MSIE 8@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " MSIE 8";
			$navigateur[2] = 8;
		} else if (preg_match("@MSIE 7@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " MSIE 7";
			$navigateur[2] = 7;
		} else if (preg_match("@MSIE 6@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " MSIE 6";
			$navigateur[2] = 6;
		} else if (preg_match("@MSIE 5@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " MSIE 5";
			$navigateur[2] = 5;
		} else if (preg_match("@MSIE@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " MSIE <= 4";
			$navigateur[2] = 4;
		} else if (preg_match("@Firefox/1@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " Firefox 1";
			$navigateur[2] = 1;
		} else if (preg_match("@Firefox/2@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " Firefox 2";
			$navigateur[2] = 2;
		} else if (preg_match("@Firefox/3@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " Firefox 3";
			$navigateur[2] = 3;
		} else if (preg_match("@Firefox/@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " Firefox";
		} else if (preg_match("@Opera/@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = "Opera";
		} else if (preg_match("@Mozilla/@", $_SERVER["HTTP_USER_AGENT"])) {
			$navigateur[1] = " Mozilla compatible Netscape";
		} else {
			$navigateur[1] = " Non déterminé";
		}
		return $navigateur;
		
	}
	
	public static function isUsingFirefox() {
		$navigateur = Fwk::getNavigateur();
		return strpos($navigateur[1], "Firefox");
	}

	public static function isUsingIE() {
		$navigateur = Fwk::getNavigateur();
		return strpos($navigateur[1], "MSIE");
	}
	
	public static function isUsingIE6OrLess() {
		$navigateur = Fwk::getNavigateur();
		return strpos($navigateur[1], "MSIE") && $navigateur[2] <= 6;
	}
	
	
	public static function isUserOnMobile() {
		$mobile = true;
		 
		 /*
		$iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$ipod   = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
		$ipad   = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
		
		if($iphone || $ipod || $ipad) {
			return true;
		} 
		*/
		 
		$arrayUserAgents = array(
			"Windows 3.1" => array("Windows 3.1", "Win3.1", "Win16"),
			"Windows 95" => array ("Windows 95", "Windows_95", "Win95"),
			"Windows 98" => array ("Windows 98", "Windows_98", "Win98"),
			"Windows NT 4.0" => array ("Windows NT 4.0", "WinNT4.0"),
			"Windows Millenium" => array ("Windows Millenium", "Windows M", "Windows_ME", "WinME"),
			"Windows 2000" => array ("Windows 2000", "Windows_2000", "Win2000", "Windows NT 5.0"),
			"Windows XP" => array ( "Windows XP", "Windows_XP", "WinXP"),
			"Windows Server 2003" => array ("Windows Server 2003", "Windows NT 5.2"),
			"Windows Vista" => array ("Windows Vista", "Windows NT 6.0"),
			"Windows NT" => array ("Windows NT", "WinNT"),
			"Mac OS"=> array ("Mac OS", "Mac_PowerPC", "Macintosh", "PPC Mac OS", "Intel Mac OS"),
			"Sun OS"=> array ("Sun OS", "SunOS"),
			"QNX" => array ("QNX"),
			"Irix"=> array ("Irix", "IRIX"),
			"Open BSD" => array ("Open BSD", "OpenBSD"),
				"Free BSD" => array ("Free BSD", "FreeBSD"),
				"Net BSD" => array ("Net BSD", "NetBSD"),
				"Linux" => array ("Linux", "X11", "Debian"),
			"BeOS"  => array ("BeOS"),
			"Windows 7" => array ("Windows NT 7.0")
			);
		 
		foreach ($arrayUserAgents as $value) {
			foreach ($value as $userAgents) {
				if (strpos($_SERVER['HTTP_USER_AGENT'], $userAgents)) {
						return false;
				}
			}
		}
		
		return true;
	}
	
	
	
	public static function unserializeJQuery($rubble = NULL) {
	    $bricks = explode('&', $rubble);
		$built = array();
	    foreach ($bricks as $key => $value) {
	        $walls = preg_split('/=/', $value);
	        if (isset($built[urldecode($walls[0])])) {
	        	if (!is_array($built[urldecode($walls[0])])) {
	        		$built[urldecode($walls[0])] = array($built[urldecode($walls[0])]);
	        	}
	        	$built[urldecode($walls[0])][] = urldecode($walls[1]);
	        } else {
	        	$built[urldecode($walls[0])] = urldecode($walls[1]);
	        }
	    }
	
	    return $built;
	}
	
	public static function getCurrentGetUrl() {
		return "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	
	
	
	/* *****************************************************
	*********************** FICHIERS ***********************
	******************************************************** */
	public static function getFileExtension($fileName) {
		return substr($fileName, Fwk::lastIndexOf($fileName, "."));
	}
	
	public static function getFileName($filePath) {
		$path = Fwk::formatePath($filePath);
		return substr($path, Fwk::lastIndexOf($path, "/") + 1);
	}
	
	public static function formatePath($filePath) {
		return str_replace("\\", "/", realpath($filePath));
	}
	
	public static function deleteFile($filePath) {
		$fh = fopen(realpath($filePath), 'w') or die("[FWK][Delete File] Can't open file '".realpath($filePath)."'");
		fclose($fh);
		unlink(realpath($filePath));
	}
	
	
	public static function writeInFile($filePath, $toWrite, $mode = 'a') {
		$monfichier = fopen($filePath, $mode);
		fputs($monfichier, utf8_decode(str_replace("\n", "\n\t\t\t", $toWrite)."\n"));
		fclose($monfichier);
	}
	
	public static function writeInOpenedFile($openedFile, $toWrite) {
		fputs($openedFile, utf8_decode(str_replace("\n", "\n\t\t\t", $toWrite)."\n"));
	}
	
	public static function getFileSize($file) {
		$size = @filesize($file);
		/*
        if ($size <= 0) {
            if (!(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'))
                $size = trim(`stat -c%s $file`);
            else{
                $fsobj = new COM("Scripting.FileSystemObject");
                $f = $fsobj->GetFile($file);
                $size = $f->Size;
            }
        }
        */
        return $size; 
	}
	
	public static function getFormatedFileSize($file) {
		$filesize = Fwk::getFileSize($file) / 1024;
		
		if ($filesize < 0) {
			return "> 2 Go";
		}
		
		$unite = "Ko";
		if ($filesize < 1024) {
			$filesize = round($filesize, 0);
		}
		// Taille en Mo
		if ($filesize > 1024) {
			$filesize = round($filesize / 1024, 2);
			$unite = "Mo";
		}
		// Taille en Go
		if ($filesize > 1024) {
			$filesize = round($filesize / 1024, 2);
			$unite = "Go";
		}
		$filesize .= " $unite";
		return $filesize;
	}
	
	
	
	/* *****************************************************
	************************ OBJETS ************************
	******************************************************** */
	public static function buildObjectFromRequest($doName) {
		if (!isset($doName) || $doName == '') {
			return null;
		}
	
		$reflectionClass = new ReflectionClass($doName);
		$newInstance = $reflectionClass->newInstance();
		$props = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

		foreach ($props as $prop) {
			if (isset($_REQUEST[$prop->getName()])) {
				if (Fwk::startsWith($prop->getName(), "is")) {
					$prop->setValue($newInstance, $_REQUEST[$prop->getName()] == "on");
				} else {
					$prop->setValue($newInstance, $_REQUEST[$prop->getName()]);
				}
			} else  {
				if (Fwk::startsWith($prop->getName(), "is") != false) {
					$prop->setValue($newInstance, false);
				}
			}
		}
		
		return $newInstance;
	}
	
	public static function buildObjectsFromDatabaseResult($results, $doToAssign) {
		$listeDo = array();
		
		$reflectionClass = new ReflectionClass($doToAssign);
		$props = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
		
		foreach($results as $result) {
			$newInstance = $reflectionClass->newInstance();
			
			foreach ($props as $prop) {
				if (isset($result[$prop->getName()])) {
					$prop->setValue($newInstance, $result[$prop->getName()]);
				}
			}
			
			$listeDo[count($listeDo)] = $newInstance;
		}
		
		return $listeDo;
	}
	
	public static function buildObjectFromUniqueDatabaseResult($result, $doToAssign) {
		if ($result == null) {
			return null;
		}
		$reflectionClass = new ReflectionClass($doToAssign);
		$props = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
		
		$newInstance = $reflectionClass->newInstance();
		
		foreach ($props as $prop) {
			if (isset($result[$prop->getName()])) {
				$prop->setValue($newInstance, $result[$prop->getName()]);
			}
		}
		
		return $newInstance;
	}
	
	
	/* ******************************************************
	************************* MAILS *************************
	********************************************************* */
	/*	param header : array (	"from" => ..., 			Optional
								"to" => ..., 			Mandatory
								"replyTo" => ..., 		Optional
		param fileMap : array (File path => Content Type);
	*/
	public static function sendMail($header, $sujet, $message, $fileMap) {
		$passageLigne = "\r\n";
		$boundary = 'didondinaditondelosdudosdodudundodudindon';
		//En-têtes du mail
		$headers = "";
		if ($header["from"] != null) {
			$headers .= "From: ".$header["from"].$passageLigne;
		}
		if ($header["replyTo"] != null) {
			$headers.= "Reply-to: ".$header["replyTo"].$passageLigne;
		}
		$headers .= "MIME-Version: 1.0$passageLigne";
		$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$passageLigne\n";

		// Corps du mail en commençant par le message principal
		$body = "--". $boundary ."\n";
		$body .= "Content-Type: text/plain; charset=UTF-8$passageLigne\n";
		$body .= "$message\n\n";
		
		foreach($fileMap as $path => $contentType) {
			// Pièce jointe
			$fichier = file_get_contents($path);
			// On utilise aussi chunk_split() qui organisera comme il faut l'encodage fait en base 64 pour se conformer aux standards
			$fichier = chunk_split( base64_encode($fichier) );

			// Écriture de la pièce jointe
			$body .= "--" .$boundary. "\n";
			$body .= "Content-Type: $contentType; name=\"".basename($path)."\"$passageLigne";
			$body .= "Content-Transfer-Encoding: base64$passageLigne";
			$body .= "Content-Disposition: attachment; filename=\"".basename($path)."\"$passageLigne\n";
			$body .= "$fichier";
		}

		// Fermeture de la frontière
		$body = $body . "--" . $boundary ."--";

		//Envoi du mail
		$destinataire = $header["to"];
		
		return mail($destinataire, $sujet, $body, $headers);
	}
	
	public static function verif_mail($email) {
		$syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
		return preg_match($syntaxe, $email);
	}
	
}


?>