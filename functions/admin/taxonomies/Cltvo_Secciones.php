<?php

class Cltvo_Secciones extends Cltvo_Taxonomy_Master
{

    const nombre_plural = 'Secciones';
    const nombre_singular = 'sección';
    const slug = 'secciones';

// args
    //const hierarchical = true; // padres eh hijos
    //const show_ui = true; // muestra para administracion
    //const query_var = true;
    //const show_admin_column = true; // columna en el administrador del port
    //protected static $postypes = array('post');

// terms iniciales
    protected static $initialTerms = array(
        "slug" => "name"
    );

}
