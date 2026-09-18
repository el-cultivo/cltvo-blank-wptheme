<?php

/**
 * Procesado de imagenes estandarizado, con esto eliminamos definidos nativos de WP y registra los requeridos en e tema
 */

 add_filter('intermediate_image_sizes_advanced', function($sizes) {
    unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['medium_large']);
    unset($sizes['large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
});

// Tamaños adaptados al tema
add_action('init', function() {
    add_image_size('cltvo-sm', 1280,  9999, false);
    add_image_size('cltvo-md', 2048, 9999, false);
    add_image_size('cltvo-lg', 2800, 9999, false);
    add_image_size('cltvo-xl', 5120, 9999, false);
});

add_filter('big_image_size_threshold', '__return_false');
add_filter('wp_img_tag_add_auto_sizes', '__return_false');

/**
 * Helper para impresion de imagenes del tema
 */

 function cltvo_image($img_id, $size, $attrs = []) {
    if (empty($img_id)) return;
    echo wp_get_attachment_image($img_id, $size, false, $attrs);
}

/**
 * Helper para videos con fallback de imagen
 * 
 * Requerido para el funcionamiento 
 * Campo video que debe retornar URL
 * Campo de imagen poster para el fallback, es requerido retornar un ID
 * 
 * Uso: cltvo_video(get_field('hero_video'), get_field('hero_poster'), ['class' => 'hero__video']);
 */

 function cltvo_video($video_url, $poster_id, $attrs = []){
    if (empty($video_url)) return;

    $poster_src = !empty($poster_id) ? wp_get_attachment_image_src($poster_id, 'cltvo-lg') : null;

    $default_attrs = [
        'autoplay'    => true,
        'loop'        => true,
        'muted'       => true,
        'playsinline' => true,
        'preload'     => 'metadata',
    ];

    $attrs = array_merge($default_attrs, $attrs);

    echo '<video';
    foreach ($attrs as $key => $value) {
        if ($value === true) echo ' ' . esc_attr($key);
        else echo ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
    }
    if ($poster_src) echo ' poster="' . esc_url($poster_src[0]) . '"';
    echo '>';
    echo '<source src="' . esc_url($video_url) . '" type="video/mp4">';
    echo '</video>';

}
