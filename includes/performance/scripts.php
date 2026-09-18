<?php

/**
 * Scripts: Se agregar defer para los internos y Partytown para los externos
 */

add_filter('script_loader_tag', function($tag, $handle, $src){
    if(is_admin()) return $tag;

    $exclude = [
        //TODO: Agregar lo que es requerido o necesario que no se cargara el defer

        'jquery-core',
    ];

    if (in_array($handle, $exclude) || strpos($tag, 'defer') !== false) return $tag;
    return str_replace(' src=', ' defer src=', $tag);
}, 10, 3);

add_action('cltvo_partytown', function() {
    // get_theme_support devuelve [false] cuando la flag está en false, por eso se revisa el valor
    $support = get_theme_support('CLTVO_PARTYTOWN');
    if ( empty($support) || empty($support[0]) ) {
        return;
    }

    // La librería vive dentro del tema, en /~partytown/
    $lib = get_template_directory_uri() . '/~partytown/';

    echo '<script>
        window.partytown = {
            lib: "' . esc_url( $lib ) . '",
            forward: ["dataLayer.push"]
        };
    </script>' . "\n";

    echo '<script src="' . esc_url( $lib . 'partytown.js' ) . '"></script>' . "\n";
});