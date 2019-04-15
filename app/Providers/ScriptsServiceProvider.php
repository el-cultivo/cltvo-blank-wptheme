<?php

namespace App\Providers;

class ScriptsServiceProvider
{
    protected $local = [];

    protected $cdn = [
        'cltvo_slick' => '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
        // 'cltvo_swiper' => '//cdnjs.cloudflare.com/ajax/libs/Swiper/4.1.6/js/swiper.min.js',
        // 'cltvo_masonry' => 'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.0/masonry.pkgd.min.js'
        // 'cltvo_youtube' => '//www.youtube.com/iframe_api',
        // 'cltvo_validate' => 'https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js',
        // 'cltvo_soundcloud' =>  'https://w.soundcloud.com/player/api.js',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Incluye el functions.js
        add_action( 'wp_enqueue_scripts', [$this, 'enqeueScripts'] );

        // // Incluye el admin-functions.js. Descomentar para tener JS en admin (no olvidar crear el file [admin-functions.js])
        // add_action( 'admin_enqueue_scripts', 'cltvo_admin_js' );
    }

    public function enqeueScripts()
    {
        $scripts = ['jquery'];

        foreach($this->cdn as $name => $url) {
            wp_register_script( $name, $url, $scripts, false, true );
            $scripts[] = $name;
        }

        foreach($this->local as $name => $path) {
            wp_register_script( $name, get_template_directory_uri() . '/js/' . $path, $scripts, false, true );
            $scripts[] = $name;
        }

        // Se agrega functions.js
        wp_register_script( 'cltvo_functions_js', get_template_directory_uri() . '/js/functions.js', $scripts, 'v1.0', true );

        // Se agregan las variables de javascript.
        wp_localize_script( 'cltvo_functions_js', 'cltvo_js_vars', $this->getJavascriptVars() );

        $scripts[] = 'cltvo_functions_js';

        foreach($scripts as $script){
            wp_enqueue_script($script);
        }
    }

    public function adminEnqeueScripts()
    {
        // wp_register_script( 'cltvo_admin_functions_js', JSPATH.'admin-functions.js', array('jquery'), false, false );
        // wp_localize_script( 'cltvo_admin_functions_js', 'cltvo_js_vars', cltvo_js_vars() );

        // wp_enqueue_style('admin-styles', get_template_directory_uri() . '/css/ultraligero_admin.css' );
        // wp_enqueue_script('jquery');
        // wp_enqueue_script('cltvo_admin_functions_js');
    }

    public function getJavascriptVars()
    {
        return [
            'site_url'      => home_url('/'),
            'template_url'  => get_bloginfo('template_url'),
            'ajax_url' 	    => admin_url( 'admin-ajax.php' ),
        ];
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
