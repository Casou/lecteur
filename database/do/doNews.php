<?php

class News {
	
	public $id;
	public $texte;
	public $date_creation;
	public $date_debut;
	public $date_fin;
	
	public static function getTableName() {
		return "lct_news";
	}
	
}