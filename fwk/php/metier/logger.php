<?php

class Logger {
	
	const LOG_LEVEL = 'DEBUG';
	const LOG_ECHO = false;
	
	private static $BALISE_DEBUT = "<pre>";
	private static $BALISE_FIN = "</pre>";
	
	private static $FILE = null;
	
	private static $log_levels = array (	
		'DEBUG' => 10,
		'INFO'	=> 20,
		'WARN'	=> 30,
		'ERROR'	=> 40,
		'FATAL'	=> 50);
	
	
	public static function init($logFileName, $relativePathToRoot = './') {
		if (Logger::$FILE == null) {
			Logger::$FILE = fopen($relativePathToRoot."logs/".$logFileName."-".date('Y-m-d').".log", 'a');
		}
	}
	
	public static function close() {
		fclose(Logger::$FILE);
		Logger::$FILE = null;
	}
	
	
	
		
	public static function debug($message) {
		if (Logger::$FILE != null) {
			if (Logger::$log_levels[Logger::LOG_LEVEL] <= Logger::$log_levels['DEBUG']) {
				Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('DEBUG', $message));
				
				if (Logger::LOG_ECHO) {
					echo Logger::$BALISE_DEBUT.$message.Logger::$BALISE_FIN;
				}
			}
		}
	}
	
	public static function info($message) {
		if (Logger::$FILE != null) {
			if (Logger::$log_levels[Logger::LOG_LEVEL] <= Logger::$log_levels['INFO']) {
				Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('INFO', $message));
				if (Logger::LOG_ECHO) {
					echo Logger::$BALISE_DEBUT.$message.Logger::$BALISE_FIN;
				}
			}
		}
	}
	
	public static function warn($message) {
		if (Logger::$FILE != null) {
			if (Logger::$log_levels[Logger::LOG_LEVEL] <= Logger::$log_levels['WARN']) {
				Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('WARN', $message));
				if (Logger::LOG_ECHO) {
					echo Logger::$BALISE_DEBUT.$message.Logger::$BALISE_FIN;
				}
			}
		}
	}

	public static function error($message, $exception = null) {
		if (Logger::$FILE != null) {
			if (Logger::$log_levels[Logger::LOG_LEVEL] <= Logger::$log_levels['ERROR']) {
				// $messageToWrite = Fwk::getDebugPrintBacktrace()."\n\nMessage :".$message;
				$messageToWrite = "Message :".$message;
				if ($exception != null) {
					$messageToWrite .= "\n".$exception->getTraceAsString();
				}
				
				Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('ERROR', $messageToWrite));
				if (Logger::LOG_ECHO) {
					echo Logger::$BALISE_DEBUT.$messageToWrite.Logger::$BALISE_FIN;
				}
			}
		}
	}
	
	public static function fatal($message) {
		if (Logger::$FILE != null) {
			if (Logger::$log_levels[Logger::LOG_LEVEL] <= Logger::$log_levels['FATAL']) {
				Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('FATAL', $message));
				if (Logger::LOG_ECHO) {
					echo Logger::$BALISE_DEBUT.$message.Logger::$BALISE_FIN;
				}
			}
		}
	}
	
	private static function getFormattedMessage($debugMode, $message) {
		return "[$debugMode] ".date('d/m/Y G:i:s')." - ".$message;
	}
	
	
	
	public static function print_r($table) {
		Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('PRINT_R', print_r($table, true)));
	}
	
	public static function printAllRequest() {
		Fwk::writeInOpenedFile(Logger::$FILE, Logger::getFormattedMessage('REQUEST', print_r($_REQUEST, true)));
	}
	
}

?>