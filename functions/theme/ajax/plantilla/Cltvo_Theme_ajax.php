<?php

class Cltvo_Theme_ajax extends Cltvo_Ajax_Master
{

	/**
	 * metodo de respuesta del ayax
	 */
	static function privMethod(){
        var_dump($_POST);
        die;
    }

}
