<?php

class DoVersion implements InterfaceDo {
	public $application;
	public $version;
	public $description;
	public $date;
	
	private static $instance;
	
	public static function getInstance() {
		if (!isset(DoVersion::$instance) || DoVersion::$instance == null) {
			DoVersion::$instance = new DoVersion();
		}
		
		return DoVersion::$instance;
	}
	
	public static function getTable() {
		return "fwk_app_version";
	}
	
	public function getTableFields() {
		return array('application', 'version', 'description', 'date');
	}
	
}

?>