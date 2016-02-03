<?php

class Cltvo_Lookbook extends Cltvo_Post_Type_master implements Cltvo_Post_Type_interface
{

    public function setMetas()
    {

    }

    static function registerPostype(){

        $labels = array(
    			'name'               => 'Lookbook',
    			'singular_name'      => 'Look',
    			'menu_name'          => 'Lookbook',
    			'name_admin_bar'     => 'Nuevo look',
    			'add_new'            => 'Crear look',
    			'add_new_item'       => 'Crear look',
    			'new_item'           => 'Nuevo look',
    			'edit_item'          => 'Modificar look',
    			'view_item'          => 'Ver look',
    			'all_items'          => 'Lookbook',
    			'search_items'       => 'Buscar look',
    			'parent_item_colon'  => 'Look relacionado',
    			'not_found'          =>'No se encontraron looks',
    			'not_found_in_trash' => 'Papelera vacía',
    		);

    	$args = array(
    		'labels'             => $labels,
    		'public'             => true,
    		'publicly_queryable' => true,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
    		'rewrite'            => array( 'slug' => 'lookbook' ),
    		'capability_type'    => 'post',
    		'has_archive'        => true,
    		'hierarchical'       => false,
    		'menu_position'      => 6,
    		'supports'           => array( 'title', 'editor')
    	);

    	//register_post_type( 'oclot_lookbook_pt', $args );

    }


}
