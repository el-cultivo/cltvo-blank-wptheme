<?php

/**
 * Los archivos ajax se incluyen como clases en la carpeta ajax
 *
 */

 define( 'THEMEAJAXDIR', get_template_directory().'/functions/theme/ajax/' ); // directorio de las clases de metabox

/** ==============================================================================================================
 *                                               agrega todos los hooks de ajax
 *  ==============================================================================================================
 */

foreach (glob(THEMEAJAXDIR.'*.php') as $filename){
	include $filename;
	$clase =  str_replace( [THEMEAJAXDIR,".php"],[""], $filename );
	$clase::registerAjax();
}
