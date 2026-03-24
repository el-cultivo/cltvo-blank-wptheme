<?php

require __DIR__.'/bootstrap/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Banderas del tema
function custom_theme_setup() {
	//Título por plugin
	add_theme_support( 'title-tag' );
	// Uso de Mailgun
    add_theme_support('CLTVO_USEMAILGUN', false);
	// Apaga/enciende comentarios globalmente
	add_theme_support('CLTVO_DISABLE_COMMENTS', true);
	// Desactivar en todos menos en algunos CPTs
	// add_theme_support('CLTVO_DISABLE_COMMENTS', ['except' => ['post']]);
}

// Enganchar la función a la acción 'after_setup_theme'
add_action('after_setup_theme', 'custom_theme_setup');

function cltvo_role_edit() {
	// get the the role object
	$role_object = get_role( 'editor' );
	
    if ( !$role_object->has_cap('edit_theme_options') )
		// add $cap capability to this role object
		$role_object->add_cap( 'edit_theme_options' );
}

add_action( 'admin_init', 'cltvo_role_edit' );