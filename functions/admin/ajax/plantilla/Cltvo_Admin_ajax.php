<?php

class Cltvo_Admin_ajax extends Cltvo_Ajax_Master
{
    const isNotAdmin = false;

	/**
	 * metodo de respuesta del ayax
	 */
	static function privMethod(){
        var_dump($_POST);
        die;
    }

}
