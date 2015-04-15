<?php

/**
 * En este archivo se incluyen los scripts
 *
 */


// add_action( 'wp_enqueue_scripts', 'cltvo_js' );
// add_action( 'admin_enqueue_scripts', 'cltvo_admin_js' ); //descomentar para tener JS en admin (no olvidar crear el file [admin-functions.js])


/**
 * SCRIPTS
 * -------
 *
 */


function cltvo_js(){
	wp_register_script( 'cltvo_functions_js', JSPATH.'functions.js', array('jquery'), false, true );
	wp_localize_script( 'cltvo_functions_js', 'cltvo_js_vars', cltvo_js_vars() );
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('cltvo_functions_js');
}

function cltvo_admin_js(){
	wp_register_script( 'cltvo_admin_functions_js', JSPATH.'admin-functions.js', array('jquery'), false, false );
	wp_localize_script( 'cltvo_admin_functions_js', 'cltvo_js_vars', cltvo_js_vars() );

	wp_enqueue_script('cltvo_admin_functions_js');
}

function cltvo_js_vars(){
	$php2js_vars = array(
		'site_url'     => home_url('/'),
		'template_url' => get_bloginfo('template_url')
	);
	return $php2js_vars;
}


?>