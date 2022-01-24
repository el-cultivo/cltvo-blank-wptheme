<?php

namespace App\Providers;

class StylesServiceProvider
{
    protected $local = [
      //'nombre' => 'path',
    ];

    protected $cdn = [
        'cltvo_slick_css' => '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Enfila todos los estilos
        add_action( 'wp_enqueue_scripts', [$this, 'enqeueStyles'] );

    }

    /**
     * Registra y enfila todos los estilos
     *
     * @return void
     */
    public function enqeueStyles()
    {
        $styles = [];

        foreach($this->cdn as $name => $url) {
            wp_register_style( $name, $url, $styles);
            $styles[] = $name;
        }

        // Registra estilos locales es bloque.
        // Pendiente resolver cómo agregar la $versión y el $media como variables

        // foreach($this->local as $name => $path) {
        //     wp_register_style( $name, get_template_directory_uri() . '/css/' . $path, $styles);
        //     $styles[] = $name;
        // }

        // Registra el style.class
        wp_register_style( 'cltvo_style_css', get_template_directory_uri() . '/style.css', $styles, '0.0');

        $styles[] = 'cltvo_style_css';

        // Enfila todos los estilos
        foreach($styles as $style){
            wp_enqueue_style($style);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
