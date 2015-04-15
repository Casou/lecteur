<?php

$LOG_LEVELS_MAP = array (	
		'DEBUG' => 'Debug',
		'INFO'	=> 'Info',
		'WARN'	=> 'Warning',
		'ERROR'	=> 'Error',
		'FATAL'	=> 'Fatal');

class FwkLogger {
	
	protected static $LOG_LEVEL = NULL;
	protected static $LOG_ECHO = false;
	
	protected static $BALISE_DEBUT = "<pre>";
	protected static $BALISE_FIN = "</pre>";
	
	protected static $FILE = null;
	protected static $FILE_PATH = null;
	
	private static $log_levels = array (	
		'DEBUG' => 10,
		'INFO'	=> 20,
		'WARN'	=> 30,
		'ERROR'	=> 40,
		'FATAL'	=> 50);
	
	
	protected static function initLogger($logFileName, $relativePathToRoot = './') {
		date_default_timezone_set('Europe/Paris');
		if (FwkLogger::$FILE == null) {
			if (!defined('PATH_LOG_FOLDER')) {
				throw new Exception("La constante PATH_LOG_FOLDER n'a pas été définie", 500);
			}
			
			FwkLogger::$FILE_PATH = $relativePathToRoot.PATH_LOG_FOLDER."/".FwkLogger::formateFileName($logFileName);
			FwkLogger::$FILE = fopen(FwkLogger::$FILE_PATH, 'a');
		}
	}
	
	protected static function formateFileName($fileName) {
		return $fileName."-".date('Y-m-d').".log";
	}
	
	public static function close() {
		fclose(FwkLogger::$FILE);
		FwkLogger::$FILE = null;
	}
	
	
	
		
	public static function debug($message) {
		if (FwkLogger::$FILE != null) {
			if (FwkLogger::$log_levels[FwkLogger::$LOG_LEVEL] <= FwkLogger::$log_levels['DEBUG']) {
				Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('DEBUG', $message));
				
				if (FwkLogger::$LOG_ECHO) {
					echo FwkLogger::$BALISE_DEBUT.$message.FwkLogger::$BALISE_FIN;
				}
			}
		}
	}
	
	public static function info($message) {
		if (FwkLogger::$FILE != null) {
			if (FwkLogger::$log_levels[FwkLogger::$LOG_LEVEL] <= FwkLogger::$log_levels['INFO']) {
				Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('INFO', $message));
				if (FwkLogger::$LOG_ECHO) {
					echo FwkLogger::$BALISE_DEBUT.$message.FwkLogger::$BALISE_FIN;
				}
			}
		}
	}
	
	public static function warn($message) {
		if (FwkLogger::$FILE != null) {
			if (FwkLogger::$log_levels[FwkLogger::$LOG_LEVEL] <= FwkLogger::$log_levels['WARN']) {
				Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('WARN', $message));
				if (FwkLogger::$LOG_ECHO) {
					echo FwkLogger::$BALISE_DEBUT.$message.FwkLogger::$BALISE_FIN;
				}
			}
		}
	}

	public static function error($message, $exception = null) {
		if (FwkLogger::$FILE != null) {
			if (FwkLogger::$log_levels[FwkLogger::$LOG_LEVEL] <= FwkLogger::$log_levels['ERROR']) {
				// $messageToWrite = Fwk::getDebugPrintBacktrace()."\n\nMessage :".$message;
				$messageToWrite = "Message :".$message;
				if ($exception != null) {
					$messageToWrite .= "\n".$exception->getTraceAsString();
				}
				
				Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('ERROR', $messageToWrite));
				if (FwkLogger::$LOG_ECHO) {
					echo FwkLogger::$BALISE_DEBUT.$messageToWrite.FwkLogger::$BALISE_FIN;
				}
			}
		}
	}
	
	public static function fatal($message) {
		if (FwkLogger::$FILE != null) {
			if (FwkLogger::$log_levels[FwkLogger::$LOG_LEVEL] <= FwkLogger::$log_levels['FATAL']) {
				Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('FATAL', $message));
				if (FwkLogger::$LOG_ECHO) {
					echo FwkLogger::$BALISE_DEBUT.$message.FwkLogger::$BALISE_FIN;
				}
			}
		}
	}
	
	private static function getFormattedMessage($debugMode, $message) {
		return "[$debugMode] ".date('d/m/Y G:i:s')." - ".$message;
	}
	
	public static function getLogFilePath() {
		return FwkLogger::$FILE_PATH;
	}
	
	
	
	public static function print_r($table) {
		Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('PRINT_R', print_r($table, true)));
	}
	
	public static function printAllRequest() {
		Fwk::writeInOpenedFile(FwkLogger::$FILE, FwkLogger::getFormattedMessage('REQUEST', print_r($_REQUEST, true)));
	}
	
}

?>