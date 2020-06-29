<?php 

return [
    
    'id'           => 'tgmpa',
	'default_path' => '',
	'menu'         => 'tgmpa-install-plugins',
	'parent_slug'  => 'themes.php',
	'capability'   => 'edit_theme_options',
	'has_notices'  => true,
	'dismissable'  => false,
	'dismiss_msg'  => '',
	'is_automatic' => true,
	'message'      => '',
	'strings'      => [
	    'page_title'                      => __( 'Instala los plugins requeridos', 'theme-slug' ),
	    'menu_title'                      => __( 'Instalar plugins', 'theme-slug' ),
	    // <snip>...</snip>
	    'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
	]
];