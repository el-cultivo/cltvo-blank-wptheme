<?php

/**
 * En este archivo se incluyen los post type personalizados  y las columnas personalizadas
 *
 */

define( 'POSTTYPESDIR', get_template_directory().'/functions/admin/posttypes/' ); // directorio de las clases de posttypes

/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

// ------------ colunmas  ---------------------------

// add_action( 'manage_posts_custom_column' , 'cltvo_tax_col', 10, 2 );
// add_action( 'manage_crdmn_proyecto_pt_posts_custom_column' , 'cltvo_tax_col', 10, 2 );

// add_filter( 'manage_edit-post_columns', 'cltvo_nueva_col_post' );
// add_filter( 'manage_edit-crdmn_proyecto_pt_columns', 'cltvo_nueva_col_proy' );



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


/** ==============================================================================================================
 *                                                inaterface
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_PostType_Interface.php';

/** ==============================================================================================================
 *                                                abstract class
 *  ==============================================================================================================
 */

	include 'Classes/Cltvo_PostType_Master.php';
	include 'Classes/Cltvo_PostTypeCustom_Master.php';


/** ==============================================================================================================
 *                                               agrega todos los objetos de posttypes
 *  ==============================================================================================================
 */

foreach (glob(POSTTYPESDIR.'*.php') as $filename){
	include $filename;
}

foreach (glob(POSTTYPESDIR.'customs/*.php') as $filename){
	include $filename;
	add_action('init', function() use ($filename) {
		$clase =  str_replace( [POSTTYPESDIR.'customs/',".php"],[""], $filename );
		$clase::registerPostype();
	});
}
