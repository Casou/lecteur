<?php

class DoParameter implements InterfaceDo {
	public $id;
	public $param_context;
	public $param_id;
	public $valeur;
	public $description;
	
	private static $instance;
	
	public static function getTable() {
		return "fwk_app_parameters";
	}
	
	public static function getInstance() {
		if (!isset(DoParameter::$instance) || DoParameter::$instance == null) {
			DoParameter::$instance = new DoParameter();
		}
		
		return DoParameter::$instance;
	}
	
	public function getTableFields() {
		return array('id', 'param_context', 'param_id', 'valeur', 'description');
	}
	
}

?>