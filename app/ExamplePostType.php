<?php 

namespace App;

use Illuminate\CustomPostType;

class ExamplePostType extends CustomPostType
{
    const nombre_plural = 'Ejemplo PostType';
    const nombre_singular = 'ejemplo PostType';
    // Slug para que coincida con el de la página.
    const slug = 'ejemplo-post-type';

    // Elementos del post type 
    protected static $supports = ['title', 'editor', 'thumbnail'];
    // Icono de dashicon para mostrar en el admin
    protected static $menu_icon = 'dashicons-laptop';

    public function setMetas()
    {

    }
}