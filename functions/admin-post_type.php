<?php

/**
 * En este archivo se incluyen los post type personalizados  y las columnas personalizadas 
 *
 */

/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

// ------------ post type ---------------------------

// add_action( 'init', 'cltvo_posttypes' ); // incluye los post types personalizados 


// ------------ colunmas  ---------------------------

// add_action( 'manage_posts_custom_column' , 'cltvo_tax_col', 10, 2 ); 
// add_action( 'manage_crdmn_proyecto_pt_posts_custom_column' , 'cltvo_tax_col', 10, 2 );

// add_filter( 'manage_edit-post_columns', 'cltvo_nueva_col_post' );
// add_filter( 'manage_edit-crdmn_proyecto_pt_columns', 'cltvo_nueva_col_proy' );



/** ==============================================================================================================
 *                                                TIPOS DE POSTS   
 *  ==============================================================================================================
 */

function cltvo_posttypes(){
	//Nombre del posttype!
	$args = array(
		'label' => 'Artistas',  								//nombre
		'public' => true,										//Público
		'rewrite' => array( 'slug' => 'artistas' ),				//Nombre en la url
		'has_archive' => true,									//Se puede usar en un archivo
		'supports' => array( 'title', 'editor', 'thumbnail' )	//Inupts UI en la página de new post
	);
	register_post_type( 'inter_artistas_pt', $args );		//Se registra

	// agrega aqui ...

}


/** ==============================================================================================================
 *                                                  Columnas en PT 
 *  ==============================================================================================================
 */


// ???
function cltvo_nueva_col_post($columns) {
	//este call back se tiene que repetir por cada
	//Post Type que se quiera afectar
	$unsets = array('author', 'categories', 'tags', 'comments', 'date');
	foreach ($unsets as $unset) unset($columns[$unset]);
	$columns['crdmn_proyecto_tax'] = 'Proyecto';
	$columns['tags'] = 'Etiquetas';
	$columns['date'] = 'Fecha';

	return $columns;
}

// ???
function cltvo_nueva_col_proy($columns) {
	//cambiar el sufijo dependiendo de el nombre del Post Type
	$unsets = array('tags', 'date');
	foreach ($unsets as $unset) unset($columns[$unset]);
	$columns['crdmn_cliente_tax'] = 'Cliente';
	$columns['crdmn_servicio_tax'] = 'Servicio';
	$columns['crdmn_proyecto_tax'] = 'Proyecto';
	$columns['tags'] = 'Etiquetas';
	$columns['date'] = 'Fecha';

	return $columns;
}

// ???
function cltvo_tax_col( $column_name, $post_id ) {
	//Muestra en la columna el nombre
	//de las taxonomías de ese post con su link
	$taxonomy = $column_name;
	$post_type = get_post_type($post_id);
	$terms = get_the_terms($post_id, $taxonomy);

	if (!empty($terms) ) {
		foreach ( $terms as $term )
			//$post_terms[] = $term->name;
			$post_terms[] ="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
		echo join('', $post_terms );
	}
}

?>