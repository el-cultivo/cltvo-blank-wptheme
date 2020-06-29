<?php 

return [

	//Yoast Seo (es requerido en producción)
	[
        'name'        => 'WordPress SEO by Yoast',
        'slug'        => 'wordpress-seo',
        //'is_callable' => 'wpseo_init',
        'required'    =>  true,
        'force_activation'   => (WP_DEBUG) ? false : true,
        'force_deactivation' => false
    ],

    //Classic editor (para mantener homogéneos los sitios y sus manuales y no cambiar la interfaz de los editores)
    [
        'name'        => 'Classic Editor',
        'slug'        => 'classic-editor',
        'required'    =>  true,
        'force_activation'   => true,
        'force_deactivation' => false
    ],

    //Post types order (Para ordenar con drag and drop los Post Types, sólo es sugerido)
    [
        'name'         => 'Post Types Order',
        'slug'         => 'post-types-order', 
        'required'    =>  false,  
        'force_activation'   => true,
        'force_deactivation' => false
    ],

    //ACF Pro (En la tienda está ACF, pero el pro requiere descarga de su sitio, así que lo agregamos de una carpeta en el tema)
    [
        'name'               => 'Advanced Custom Fields Pro',
        'slug'               => 'advanced-custom-fields-pro',
        'source'             => get_template_directory() . '/includes/plugins/advanced-custom-fields-pro.zip',
        'required'           => true,
        'version'            => '5.8.12',
        'force_activation'   => true,
        'force_deactivation' => false,
        'external_url'       => '',
        'is_callable'        => '',
    ],


    //ACF Pro (En la tienda está ACF, pero el pro requiere descarga de su sitio, así que lo agregamos de una carpeta en el tema)
    [
        'name'               => 'WPML Multilingual CMS',
        'slug'               => 'sitepress-multilingual-cms',
        'source'             => get_template_directory() . '/includes/plugins/sitepress-multilingual-cms.zip',
        'required'           => false,
        'version'            => '4.3.16',
        'force_activation'   => true,
        'force_deactivation' => false,
        'external_url'       => '',
        'is_callable'        => '',
    ],
];