<?php 

namespace Illuminate\Foundation;

class Application
{
    public $path;

    public $config;

    public function __construct($path)
    {
        define( 'BLOGURL', get_home_url('/') );
        define( 'THEMEURL', get_bloginfo('template_url') . '/' );
        define( 'TRANSDOMAIN', wp_get_theme()->template );

        $this->path = $path;

        // Cargamos el archivo config del tema. 
        $this->config = require $this->path.'/config/app.php';
        
        foreach($this->config['providers'] as $provider) {
            $object = new $provider($this);
            $object->boot();
        }
    }
}