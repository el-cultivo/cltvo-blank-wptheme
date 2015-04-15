<?php

define( 'JSPATH', get_template_directory_uri() . '/js/' );
define( 'BLOGURL', get_home_url('/') );
define( 'THEMEURL', get_bloginfo('template_url').'/' );


/** 
 * Array de paginas especificas o especiales 
 *
 * Key: nombre de la pagina especial 
 * Value: id corresponciente a la pagina especial 
 *
 */


$special_pages = array(
						'example' => 9
						);


/** .................................................................................................
 * Inluye los archivos generarles 
 *
 */


// ---------------- scripts
// Contiene la llamada de los archivos functions.js y admin-functions.js asi como inclucion de valiables java  

include_once('functions/general-scripts_js.php');

// ---------------- funciones cltvo 
// Contiene las funciones generales del cultivo que son independeites de cada proyecto 

include_once('functions/general-functions_cltvo.php');

// ---------------- filtros generales
// ???

include_once('functions/general-filters.php');


/** .................................................................................................
 * Inluye los archivos de admin  
 *
 */

// ---------------- personaizacion del menu
// Contiene las funciones para personalizar el menu del admin 

include_once('functions/admin-menu.php');

// ---------------- imagenes de tamaños y opcciones personalizadas
// Contiene la funciones para personalizar los tamaños de la imagenes  

include_once('functions/admin-images.php');

// ---------------- post type y taxonimias 
// Contiene el registro de tipos de post persializados y configuracion del editor de los mismos

include_once('functions/admin-post_type.php');

// Contiene el registro de taxonomias personalizadas

include_once('functions/admin-taxonomies.php');

// ---------------- meta boxes y save post 
// Contiene la inclucion de las metaboxes asi como las funciones del save post 

include_once('functions/admin-metabox_savepost.php');


/** .................................................................................................
 * Inluye los archivos del thema   
 *
 */

// ---------------- filtros del tema 
// Se incluyen los filtros especificos del tema 
include_once('functions/theme-filters.php');

/* ---------------- funciones del tema  -------------------------------*/
// Se incluyen las funciones especificas del tema
include_once('functions/theme-functions.php');



?>