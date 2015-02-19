<?php

class Tag {
	
	public $id;
	public $label;
	
	public static function getTableName() {
		return "lct_tag";
	}
	
	public static function getJoinVideoTableName() {
		return "lct_video_tag";
	}
	
}