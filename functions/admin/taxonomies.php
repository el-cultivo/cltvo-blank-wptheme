<?php

/**
 * En este archivo se incluyen las taxonomías personalizadas
 *
 */
 define( 'TAXONOMYSDIR', get_template_directory().'/functions/admin/taxonomies/' ); // directorio de las clases de taxonomias

/** ==============================================================================================================
 *                                               TAXONOMÍAS
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_Taxonomy_interface.php';


/** ==============================================================================================================
 *                                               agrega todos los objetos de taxonomias
 *  ==============================================================================================================
 */


foreach (glob(TAXONOMYSDIR.'*.php') as $filename){
	include $filename;
	add_action('init', function() use ($filename) {
		$clase =  str_replace( [TAXONOMYSDIR,".php"],[""], $filename );
		$clase::registerTaxonomy();
	});
}


?>
