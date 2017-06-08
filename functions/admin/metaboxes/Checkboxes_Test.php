<?php 
require_once 'masters/Cltvo_Checkboxes.php';

class Checkboxes_Test extends Cltvo_Checkboxes
{
	/* */
	public static $opciones = [ 
		'opt1'	=> 'opcion 1',
		'opt2'	=> 'opcion 2',
	];

	public static function metaboxDisplayRule(){
		return true;
	}
}