<?php

class Cltvo_Taxonomy extends Cltvo_Taxonomy_Master
{

    const nombre_plural = 'Taxonomies';
    const nombre_singular = 'Taxonomy';
    const slug = 'taxonomies';

// args
    //const hierarchical = true; // padres eh hijos
    //const show_ui = true; // muestra para administracion
    //const query_var = true;
    //const show_admin_column = true; // columna en el administrador del port
    //protected static $postypes = array('post');

// terms iniciales
    protected static $initialTerms = array(
        // "slug" => "name"
    );

}
