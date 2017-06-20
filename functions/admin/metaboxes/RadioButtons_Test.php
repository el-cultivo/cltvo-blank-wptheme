<?php 

require_once 'masters/Cltvo_RadioButtons.php';

class RadioButtons_Test extends Cltvo_RadioButtons
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