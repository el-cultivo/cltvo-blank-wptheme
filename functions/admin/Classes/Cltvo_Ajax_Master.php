<?php

abstract class Cltvo_Ajax_Master implements Cltvo_Ajax_Interface{

	const isNotAdmin = true;

	/**
	 * registra las ajax
	 */
	final static function registerAjax(){
		$className = get_called_class();

		if ( $className::isNotAdmin ) { // solo si no es admin
			add_action( "wp_ajax_nopriv_".$className, function() use ($className) {
				$className::noPrivMethod();
			});
		}

		add_action( "wp_ajax_".$className,function() use ($className){
			$className::privMethod();
		});
	}


	/**
	 * metodo de respuesta del ayax no privado
	 */
	static function noPrivMethod(){
		$className = get_called_class();
		$className::privMethod();
	}

	// /**
	//  * metodo de respuesta del ayax
	//  */
	// abstract static function privMethod();

}
