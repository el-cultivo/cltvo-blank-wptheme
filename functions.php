<?php
/** ==============================================================================================================
 *                                       Constantes y variables Globales
 *  ==============================================================================================================
 */
define( 'JSPATH', get_template_directory_uri() . '/js/' );
define( 'CSSPATH', get_template_directory_uri() . '/css/' ); // css para el adimin
define( 'BLOGURL', get_home_url('/') );
define( 'THEMEURL', get_bloginfo('template_url').'/' );

// $theme_info = wp_get_theme(); // inicializacion de la traduccion
// define( 'TRANSDOMAIN', $theme_info->template );


 // ---------------- paginas especiales
 // Hook que crea las paginas especificas o especiales de manera automatica

 include_once('functions/special_pages.php');

/** ==============================================================================================================
 *                                       Inluye los archivos generarles
 *  ==============================================================================================================
 */
// ---------------- scripts
// Contiene la llamada de los archivos functions.js y admin/functions.js asi como inclucion de valiables java

include_once('functions/general/scripts_js.php');

// ---------------- funciones cltvo
// Contiene las funciones generales del cultivo que son independeites de cada proyecto

include_once('functions/general/functions_cltvo.php');

// ---------------- flitros cltvo
// Contiene los filtros generales del cultivo que son independeites de cada proyecto

include_once('functions/general/filters_cltvo.php');




/** ==============================================================================================================
 *                                       Inluye los archivos de admin
 *  ==============================================================================================================
 */

// ---------------- personaizacion del menu
// Contiene las funciones para personalizar el menu del admin

include_once('functions/admin/menu.php');

// ---------------- imagenes de tamaños y opcciones personalizadas
// Contiene la funciones para personalizar los tamaños de la imagenes

include_once('functions/admin/images.php');

// ---------------- post type y taxonimias
// Contiene el registro de tipos de post persializados y configuracion del editor de los mismos

include_once('functions/admin/post_type.php');

// Contiene el registro de taxonomias personalizadas

include_once('functions/admin/taxonomies.php');

// ---------------- meta boxes y save post
// Contiene la inclucion de las metaboxes asi como las funciones del save post

include_once('functions/admin/metabox.php');

// ---------------- ajax del admin
// Contiene los funciones ajax  del admin

include_once('functions/admin/ajax.php');

/** ==============================================================================================================
 *                                         Inluye los archivos del tema
 *  ==============================================================================================================
 */

// ---------------- funciones del menu
// Contiene el menú del sitio y sus funciones

include_once('functions/theme/menu.php');

// ---------------- filtros del tema
// Contiene los filtros específicos del tema

include_once('functions/theme/filters.php');

// ---------------- funciones del tema
// Contiene los funciones específicas del tema

include_once('functions/theme/functions.php');

// ---------------- ajax del tema
// Contiene los funciones ajax especificas del tema

include_once('functions/theme/ajax.php');


?>
