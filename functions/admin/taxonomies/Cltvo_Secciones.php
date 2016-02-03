<?php

class Cltvo_Secciones implements Cltvo_Taxonomy_interface
{

    static function registerTaxonomy(){

        $nombre_genero = 'comprar';
    	$nombre_Tienda = 'Tienda';
    	$nombre_Tienda_en_linea = 'Tienda en línea';


    	// genero
    	$labels = array(
    		'name'                       =>  $nombre_Tienda_en_linea,
    		'singular_name'              =>  $nombre_Tienda_en_linea,
    		'search_items'               => 'Buscar'." ".$nombre_genero,
    		'all_items'                  => 'Todos',
    		'parent_item'                =>  NULL,
    		'parent_item_colon'          =>  NULL,
    		'edit_item'                  => 'Editar'." ".$nombre_genero,
    		'update_item'                => 'Actualizar'." ".$nombre_genero,
    		'add_new_item'               => 'Agregar'." ".$nombre_genero,
    		'new_item_name'              => 'Nuevo'." ".$nombre_genero,
    		'separate_items_with_commas' => 'Separar cada uno con una coma.',
    		'add_or_remove_items'        => 'Agregar o quitar'." ".$nombre_genero,
    		'choose_from_most_used'      => 'Mas usados »',
    		'menu_name'                  => 'Comprar'
    	);

    	$args = array(
    		'hierarchical' 		=> true,
    		'labels'       		=> $labels,
    		'show_ui'      		=> true,
    		'query_var'    		=> true,
    		'not_found'    		=> 'lorem',
    		'rewrite'      		=> array( 'slug' => 'tienda' ),
    		'show_admin_column'	=> true
    	);

    	//register_taxonomy( 'tienda', array( 'post' ), $args ); // ----------------- nombre generico

    }


}
