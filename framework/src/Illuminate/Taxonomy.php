<?php

namespace Illuminate;

use Illuminate\Contracts\Taxonomy as TaxonomyContract;

class Taxonomy implements TaxonomyContract
{
	const nombre_plural = 'Cltvo_Taxonomy';

	const nombre_singular = 'Cltvo_Taxonomy';

    const slug = 'Cltvo_Taxonomy';

	const hierarchical = true;

	const show_ui = true;

	const query_var = true;

	const show_admin_column = true;

	protected static $postypes = ['post'];

    protected static $initialTerms = [];

	static function registerTaxonomy()
	{
    	$labels = array(
    		'name'                       =>  static::nombre_plural,
    		'singular_name'              =>  static::nombre_singular,
    		'search_items'               => 'Buscar'." ".static::nombre_plural,
    		'all_items'                  => 'Todos',
    		'parent_item'                =>  NULL,
    		'parent_item_colon'          =>  NULL,
    		'edit_item'                  => 'Editar'." ".static::nombre_singular,
    		'update_item'                => 'Actualizar'." ".static::nombre_singular,
    		'add_new_item'               => 'Agregar'." ".static::nombre_singular,
    		'new_item_name'              => 'Nuevo'." ".static::nombre_singular,
    		'separate_items_with_commas' => 'Separar cada uno con una coma.',
    		'add_or_remove_items'        => 'Agregar o quitar'." ".static::nombre_plural,
    		'choose_from_most_used'      => 'Mas usados »',
    		'menu_name'                  => static::nombre_plural
    	);

    	$args = array(
    		'hierarchical' 		=> static::hierarchical,
    		'labels'       		=> $labels,
    		'show_ui'      		=> static::show_ui,
    		'query_var'    		=> static::query_var,
    		'not_found'    		=> 'lorem',
    		'rewrite'      		=> array( 'slug' =>  static::slug ),
    		'show_admin_column'	=> static::show_admin_column
    	);

    	static::registerTaxonomyHook( $args);
        static::setInitialTerms();

	}

	public static function classname()
    {
        $classname = get_called_class();

        $shortname = basename(str_replace('\\', '/',  $classname));

        return toSnakeCase($shortname);
    }

	final static function registerTaxonomyHook( array $args)
	{
        register_taxonomy( static::classname(), static::$postypes , $args );
    }

    /**
	 * registra los terms inicales
	 */
	static function setInitialTerms()
	{
        foreach (static::$initialTerms as $slug => $name) {
    		if( ! term_exists($slug, static::classname()) ){
    			wp_insert_term(
    				$name,
    				strtolower(static::classname()),
    				array( 'slug' => $slug)
    			);
    		}
    	}
    }

    /**
     * trae los terms de esa taxonomia
     */
	static function getTerms(array $args = array())
	{
        return get_terms ( static::classname() ,$args );
    }
}
