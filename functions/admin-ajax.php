<?php

/**
 * En este archivo se incluyen las funciones ajax del admin
 *
 */


/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

//.................................... Show productos
 //add_action( 'wp_ajax_nopriv_show_productos', 'show_productos' );
 //add_action( 'wp_ajax_show_productos', 'show_productos' );

/** ==============================================================================================================
 *                                                FUNCIONES
 *  ==============================================================================================================
 */

/**
 * callback que lleva el producto al #mas-info-productos
 */
function show_servicios() {

    if (isset($_POST['post_id'])) {

    	$post = get_post($_POST['post_id']);
    	include get_template_directory().'/inc/servicio.php';
    }else{
    	echo 'error';
    }
    die();

}
