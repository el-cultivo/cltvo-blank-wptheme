<?php

/**
 * Los ajax se incluyen como clases en la carpeta ajax
 *
 */

 define( 'ADMINAJAXDIR', get_template_directory().'/functions/admin/ajax/' ); // directorio de las clases de metabox

/** ==============================================================================================================
 *                                                inaterface
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_Ajax_Interface.php';

/** ==============================================================================================================
 *                                                abstract class
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_Ajax_Master.php';

/** ==============================================================================================================
 *                                               agrega todos los hooks de ajax
 *  ==============================================================================================================
 */

foreach (glob(ADMINAJAXDIR.'*.php') as $filename){
	include $filename;
	$clase =  str_replace( [ADMINAJAXDIR,".php"],[""], $filename );
	$clase::registerAjax();

}
