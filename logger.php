<?php

class Logger extends FwkLogger {
	
	public static function reinit($relativePathToRoot = './') {
		FwkLogger::close();
		Logger::init($relativePathToRoot);
	}
	
	public static function init($relativePathToRoot = './') {
		Logger::$LOG_LEVEL = Logger::getLogLevel();
		
		$logFileName = LOG_FILE_NAME;
		if (isset($_SESSION["user"])) {
			$logFileName = $_SESSION["user"]."_".$logFileName;
		}
		
		FwkLogger::initLogger($logFileName, $relativePathToRoot);
	}
	
	public static function getLogFilePathForUser($user_login) {
		return FwkLogger::formateFileName($_SESSION["user"]." - ".$user_login);
	}
	
	private static function getLogLevel() {
		if (isset($_SESSION['log_level'])) {
			return $_SESSION['log_level'];
		} else {
			return FwkParameter::getParameter(PARAM_CONTEXT_LOG, PARAM_ID_LOG_DEFAULT_LEVEL);
		}
	}
	
}

?>