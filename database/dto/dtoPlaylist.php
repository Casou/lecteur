<?php

class PlaylistDTO {
	
	public $playlist;
	public $videos;

	public $nbVideos;
	public $can_read;
	public $can_read_plus;
	public $can_write;
	public $can_share;
	public $creator;

	public function __construct() {
	}
}

?>