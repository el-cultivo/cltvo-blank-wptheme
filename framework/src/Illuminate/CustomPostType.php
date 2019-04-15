<?php

namespace Illuminate;

use Illuminate\Contracts\CustomPostType as CustomPostTypeContract;

abstract class CustomPostType extends PostType implements CustomPostTypeContract
{
	const nombre_plural = 'Cltvo_PostTypeCustom';
	
	const nombre_singular = 'Cltvo_PostTypeCustom';
	
    const slug = 'Cltvo_PostTypeCustom';

	const publico = true;

	const publicly_queryable = true;

	const show_ui = true;

	const show_in_menu = true;

	const query_var = true;

	const capability_type = 'post';

	const has_archive = true;

	const hierarchical = false;

	const menu_position = 6;

	protected static $supports = ['title', 'editor'];

	protected static $taxonomies = [];

	protected static $menu_icon = '';

	static function registerPostype()
	{
        $labels = [
			'name'               => static::nombre_plural,
			'singular_name'      => static::nombre_plural,
			'menu_name'          => static::nombre_plural,
			'name_admin_bar'     => 'Nuevo '.static::nombre_singular,
			'add_new'            => 'Crear '.static::nombre_singular,
			'add_new_item'       => 'Crear '.static::nombre_singular,
			'new_item'           => 'Nuevo '.static::nombre_singular,
			'edit_item'          => 'Modificar '.static::nombre_singular,
			'view_item'          => 'Ver '.static::nombre_singular,
			'all_items'          => static::nombre_plural,
			'search_items'       => 'Buscar '.static::nombre_singular,
			'parent_item_colon'  => static::nombre_plural.' relacionados',
			'not_found'          =>	'No se encontraron '.static::nombre_plural,
			'not_found_in_trash' => 'Papelera vacía',
		];

    	$args = array(
    		'labels'             => $labels,
    		'public'             => static::publico,
    		'publicly_queryable' => static::publicly_queryable,
    		'show_ui'            => static::show_ui,
    		'show_in_menu'       => static::show_in_menu,
    		'query_var'          => static::query_var,
    		'rewrite'            => array( 'slug' => static::slug ),
    		'capability_type'    => static::capability_type,
    		'has_archive'        => static::has_archive,
    		'hierarchical'       => static::hierarchical,
    		'menu_position'      => static::menu_position,
			'supports'           => static::$supports,
			'taxonomies'		 => static::$taxonomies,
			'menu_icon'			 => static::$menu_icon
    	);

    	static::registerPostypeHook($args);
    }

    public static function classname()
    {
        $classname = get_called_class();

        $shortname = basename(str_replace('\\', '/',  $classname));

        return toSnakeCase($shortname);
    }

	final static function registerPostypeHook(array $args)
	{
        register_post_type(static::classname(), $args);
    }

}
