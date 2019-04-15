<?php

function getFilters()
{

    global $wp_query;

    // Revisa si estamos en un archivo de postype.
    $post_type = get_current_post_type();

    if(!$post_type){
        return [];
    }

    $filters = []; //

    // Make filters
    $taxonomies = get_object_taxonomies($post_type, 'objects');

    foreach($taxonomies as $taxonomy => $object){

        $filters[$taxonomy] = [
            'name' => $object->label,
            'terms' => []
        ]; //

        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

        foreach($terms as $term) {

            $filters[$taxonomy]['terms'][$term->slug] = [
                'name' => $term->name,
                'active' => is_active_filter($term, $taxonomy),
                'permalink' => build_filter_permalink($term, $taxonomy)
            ];

        }

    }

    return $filters;
}

function is_active_filter($term, $taxonomy)
{
    $query_args = $_GET;

    if (!get_query_param($taxonomy)) {
        return false;
    }

    $url_terms = explode('+', get_query_param($taxonomy));

    return in_array($term->slug, $url_terms);
}

function current_url()
{
    return strtok($_SERVER['REQUEST_URI'], '?');
}

function get_query_param($key)
{
    if(array_key_exists($key, $_GET) && strlen($_GET[$key]) != 0){
        return $_GET[$key];
    }

    return '';
}

function build_paginate_links($args = [])
{
    if(!array_key_exists('total', $args)){
        global $wp_query;
        $args['total'] = $wp_query->max_num_pages;
    }

    if(!array_key_exists('current', $args)){
        $args['current'] = max( 1, intval(get_query_param('pag')) );
    }

    $links = [
        'prev' => '',
        'pages' => [],
        'next' => ''
    ];

    // Building pages
    for($i = 1; $i <= $args['total']; $i++){

        $links['pages'][] = [
            'current' => $args['current'] == $i,
            'url' => build_paginate_filter_permalink($i),
            'page' => $i
        ];

    }

    // Building prev
    if(($prev = $args['current'] - 1) >= 1){
        $links['prev'] = build_paginate_filter_permalink( $prev );
    }

    // Building next
    if(($next = $args['current'] + 1) <= $args['total']){
        $links['next'] = build_paginate_filter_permalink( $next );
    }

    return $links;
}

function get_current_post_type($args = [])
{
    if(array_key_exists('taxonomy', $args)) {

        return get_taxonomy($args['taxonomy'])->object_type[0];

    }

    global $wp_query;

    // Buscamos el post_type en el query arg.
    if(array_key_exists('post_type', $wp_query->query)){

        return $wp_query->query['post_type'];

    }

    // Buscamos el post_type en el queried object.
    if(property_exists($wp_query, 'queried_object') && $wp_query->queried_object instanceof WP_Term){

        return get_taxonomy($wp_query->queried_object->taxonomy)->object_type[0];

    }
}

function build_filter_permalink($term, $taxonomy)
{
    $query_args = $_GET;

    unset($query_args['pag']);

    // Revisamos si ya hay filtro para esa taxonomia
    if (get_query_param($taxonomy)) {

        // Obtenemos los terminos que están en ese filtro.
        $url_terms = explode('+', $query_args[$taxonomy]);

        // Revisamos si el término ya se está aplicando al filtro
        if(in_array($term->slug, $url_terms)) {

            // Eliminamos el termino del array.
            $url_terms = array_filter($url_terms, function($url_term) use ($term){
                return $url_term != $term->slug;
            });

            if (count($url_terms) == 0) {

                unset($query_args[$taxonomy]);

            } else {

                $query_args[$taxonomy] = implode('+', $url_terms);

            }

        }else {

            // Si no existe lo agregamos.
            $url_terms[] = $term->slug;
            $query_args[$taxonomy] = implode('+', $url_terms);

        }

    }else {

        $query_args[$taxonomy] = $term->slug;

    }

    return build_querieable_permalink($query_args);
}

function build_orderby_filter_permalink($orderby, $meta_key = false, $force_order = false)
{
    $query_args = $_GET;

    if(!$force_order){
        if($meta_key) {
            $need_order = array_key_exists('orderby', $query_args) && $query_args['orderby'] == 'meta_value' && array_key_exists('meta_key', $query_args) && $query_args['meta_key'] == $orderby;
        }else {
            $need_order = array_key_exists('orderby', $query_args) && $query_args['orderby'] == $orderby;
        }

        if($need_order) {
            if(array_key_exists('order', $query_args)){
                $query_args['order'] = strtolower($query_args['order']) == 'asc' ? 'desc' : 'asc';
            }else {
                $query_args['order'] = 'asc';
            }
        }
    }else {
        $query_args['order'] = $force_order;
    }

    if($meta_key){

        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = $orderby;

    }else {

        $query_args['orderby'] = $orderby;
        unset($query_args['meta_key']);

    }

    return build_querieable_permalink($query_args);
}

function build_order_filter_permalink($order = 'asc')
{
    return build_querieable_permalink(['order' => $order]);
}

function build_starts_with_filter_permalink($letter)
{
    return build_querieable_permalink(['starts_with' => $letter]);
}

function build_paginate_filter_permalink($page)
{
    return build_querieable_permalink(['pag' => $page]);
}

function build_querieable_permalink($args) {
    return current_url() . '?' . http_build_query(
        array_merge($_GET, $args)
    ) . '#query';
}

function the_typology($post = 0, $classname = '')
{
    $post = get_post( $post );

    $typology = get_terms_of_post($post, $post->post_type . '_typology');

    $mundo = 'Hahaha';

    if($typology){
        foreach(get_terms_of_post($post, $post->post_type . '_typology') as $term){
            $permalink = esc_url( get_permalink($post) );
            echo "<a class='$classname' href='$permalink'>$term->name</a>";
        }
    }
}

