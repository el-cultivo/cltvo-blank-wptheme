<?php

define( 'JSPATH', get_template_directory_uri() . '/js/' );
define( 'BLOGURL', get_home_url('/') );
define( 'THEMEURL', get_bloginfo('template_url').'/' );

add_theme_support( 'post-thumbnails' );
add_action( 'init', 'cltvo_posttypes' );
add_action( 'init', 'cltvo_custom_tax' );
add_action( 'add_meta_boxes', 'cltvo_metaboxes' );
add_action( 'save_post', 'cltvo_save_post' );
add_action( 'wp_enqueue_scripts', 'cltvo_js' );



/*	SCRIPTS
	-------
*/

function cltvo_js(){
	wp_register_script('cltvo_functions_js', JSPATH.'functions.js', array('jquery'), false, true );

	$php2js_vars = array(
		'site_url'     => home_url('/'),
		'template_url' => get_bloginfo('template_url')
	);
	wp_localize_script( 'cltvo_functions_js', 'php2js_vars', $php2js_vars );
	
	
	if( !is_admin() ){
		wp_enqueue_script('jquery');
		wp_enqueue_script('cltvo_functions_js');
	}	
}



/*	TIPOS DE POSTS
	--------------
*/

function cltvo_posttypes(){
	// Cultiva Código
}



/*	TAXONOMÍAS
	----------
*/
	
function cltvo_custom_tax(){
	// Cultiva Código
}



/*	META CAJAS
	----------
*/
	
function cltvo_metaboxes(){
	// Cultiva Código
}



/*	AL GUARDAR EL POST
	------------------
*/
	
function cltvo_save_post($id){
	// Permisos
	if( !current_user_can('edit_post', $id) ) return $id;

	// Vs Autosave
	if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return $id;
	if( wp_is_post_revision($id) OR wp_is_post_autosave($id) ) return $id;

	// Cultiva Código...
}




?>