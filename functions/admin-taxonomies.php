<?php

/**
 * En este archivo se incluyen las taxonomías personalizadas  
 *
 */

/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

// add_action( 'init', 'cltvo_custom_tax' ); // incluye las taxonomias personalizadas 


/** ==============================================================================================================
 *                                               TAXONOMÍAS
 *  ==============================================================================================================
 */

function cltvo_custom_tax(){
	//Nombre de la taxonomía
	$argumentos = array(						
		'labels' => array(
			'name'			=> 'Secciones',			//Nombre
			'add_new_item'	=> 'Nueva Sección',		//Nombre del botón para agregar nuevo término
			'parent_item'	=> 'Sección madre'		//Asignar el término a un término padre
		),
		'hierarchical' => true
	);
	
	register_taxonomy(
		'inter_seccion_tax',						//nombre de la tax
		'inter_activi_pt',							//a qué posttype pertenece
		$argumentos
	);	

	// agrega aquí ...
}



?>