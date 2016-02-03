<?php

/**
 * Los metabox se incluyen como clases en la carpeta metaboxes
 *
 */

 define( 'METABOXDIR', get_template_directory().'/functions/admin/metaboxes/' ); // directorio de las clases de metabox

/** ==============================================================================================================
 *                                                inaterface
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_metabox_interface.php';

/** ==============================================================================================================
 *                                                abstract class
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_metabox_master.php';

/** ==============================================================================================================
 *                                               agrega todos los metaboxes
 *  ==============================================================================================================
 */

foreach (glob(METABOXDIR.'*.php') as $filename){
	include $filename;
	add_action('admin_init', function() use ($filename) {
		$clase =  str_replace( [METABOXDIR,".php"],[""], $filename );
		new $clase;
	});
}
