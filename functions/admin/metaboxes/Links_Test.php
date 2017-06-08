<?php 
require_once 'masters/Cltvo_Links.php';

class Links_Test extends Cltvo_Links
{
	/* */
	public static $links = [ 
		'link1'	=> 'link 1',
		'link2'	=> 'link 2',
	];

	public static function metaboxDisplayRule(){
		return true;
	}
}