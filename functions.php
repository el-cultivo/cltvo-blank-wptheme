<?php

require __DIR__.'/bootstrap/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Uso de Mailgun
function custom_theme_setup() {
    add_theme_support('CLTVO_USEMAILGUN', false);
}

// Enganchar la función a la acción 'after_setup_theme'
add_action('after_setup_theme', 'custom_theme_setup');
