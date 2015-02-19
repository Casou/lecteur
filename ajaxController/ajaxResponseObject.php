<?php

DEFINE('AJAX_STATUS_OK', 'OK');
DEFINE('AJAX_STATUS_WARNING', 'WARNING');
DEFINE('AJAX_STATUS_KO', 'KO');

DEFINE('AJAX_STATUS_RUNNING', 'RUNNING');

class AjaxResponseObject {
	
	public $status;
	public $message;
	public $infos;
	
	function __construct($status, $message = null, $infos = null) {
		$this->status = $status;
		$this->message = $message;
		$this->infos = $infos;
	}
	
}

?>