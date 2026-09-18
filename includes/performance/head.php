<?php
/**
 * Head: Se agregan los preconects que sean requeridos
 */


// TODO: agregar o quitar según lo que use el proyecto
add_action('cltvo_preconnect', function() {
    $links = [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
        // 'https://cdn-proyecto.s3.amazonaws.com',
    ];

    foreach ($links as $link) {
        $crossorigin = str_contains($link, 'gstatic') ? ' crossorigin' : '';
        echo '<link rel="preconnect" href="' . esc_url($link) . '"' . $crossorigin . '>' . "\n";
    }
});


// TODO: cambiar 'hero_img' por el nombre real del campo ACF del proyecto
add_action('cltvo_lcp_preload', function() {
    if ( ! function_exists('get_field') ) return;

    $current_id = get_queried_object_id();
    $img_id     = get_field('hero_img', $current_id);

    if ( is_array($img_id) ) $img_id = $img_id['ID'] ?? null;
    if ( empty($img_id) ) return;

    $src    = wp_get_attachment_image_src($img_id, 'cltvo-lg');
    $srcset = wp_get_attachment_image_srcset($img_id, 'cltvo-lg');

    if ( !$src ) return;

    echo '<link rel="preload" as="image" href="' . esc_url($src[0]) . '" ';
    if ($srcset) echo 'imagesrcset="' . esc_attr($srcset) . '" imagesizes="100vw"';
    echo ">\n";
});


 