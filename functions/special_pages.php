<?php

add_action('init', function(){

/**
 * Array de paginas especificas o especiales
 * Key: slug de la pagina especial
 * Value: array primer valor nombre segundo padre slug
 *
 */

 $special_pages = array(
     'clientes' => array(
             'Clientes', // nombre de la pagina
             "" // slug de de la pagina padre siempre se debe registar la pagina padre
         ) ,
     'contacto' => array(
             'Contacto',  // nombre de la pagina
             ""  // slug de de la pagina padre siempre se debe registar la pagina padre
         )
 );

 $special_pages_ids = get_option('special_pages_ids'); // almacena los ids de las paginas especiales

 if ( !is_array($special_pages_ids) )  { //crea la opccion si aun no esta creada
     add_option('special_pages_ids');
     $special_pages_ids=array();
 }

 foreach ($special_pages as $slug => $args) { // genera y revisa las paginas
     $CreaPost = true;
     if( isset($special_pages_ids[$slug]) ){ // si ya se ha creado

         $special_pages_ids[$slug] = intval($special_pages_ids[$slug]);
         $pagina = get_post( $special_pages_ids[$slug] );

         if ( $pagina ) { // si no borraron permanentemente la pagina
             $CreaPost = false;
             $actualizar = false;

             $pagina_sustituta = get_page_by_path($slug);

             if($pagina_sustituta){ // verifica que el slug no lo tenga otra pagina
                 $pagina = ($pagina_sustituta->ID != $pagina->ID ) ? $pagina_sustituta : $pagina ;
                 $special_pages_ids[$slug] = $pagina->ID;
             }

             $pagina_args = array(
                 'ID'           =>   $pagina->ID,
                 'post_title'   =>   $pagina->post_title,
                 'post_content' =>   $pagina->post_content,
             );
         // si no esta publicada
             if ( $pagina->post_status != 'publish' ){ // evita que las paginas se coloquen en borador o se envien a la papelera.
                 $pagina_args['post_status'] = 'publish';
                 $actualizar = true;
             }

         // si modificaron el post parent
             $parent =  empty($args[1]) ? 0 : $special_pages_ids[ $args[1] ];

             if ( $pagina->post_parent != $parent ){ // evita que las paginas se cambien de padre
                 $pagina_args['post_parent'] = $parent;
                 $actualizar = true;
             }

         // si modificaron el slug
             if ( $pagina->post_name != $slug ){ // evita que las paginas se cambien de slug
                 $pagina_args['post_name'] = $slug;
                 $actualizar = true;
             }

             if( $actualizar ){
                 wp_update_post( $pagina_args );
             }
         }
     }

     if( $CreaPost ){ // si no existe la pagina guarda

         $page = array(
             'post_author'  => 1,
             'post_status'  => 'publish',
             'post_name'    => $slug,
             'post_title'   => $args[0],
             'post_type'    => 'page',
             'post_parent'  => empty($args[1]) ? 0 : $special_pages_ids[ $args[1] ]
             );

         $special_pages_ids[$slug] = wp_insert_post( $page, true );
     }
 }

 update_option('special_pages_ids',$special_pages_ids);
 $GLOBALS['special_pages_ids'] = $special_pages_ids;

});
