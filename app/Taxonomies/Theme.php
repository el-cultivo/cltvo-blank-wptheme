<?php

namespace App\Taxonomies;

use Illuminate\Taxonomy;

class ExampleTaxonomie extends Taxonomy
{
    const nombre_plural = 'Ejemplo Taxonomías';
    const nombre_singular = 'Ejemplo taxonomía';
    // Slug que coincida con página
    const slug = 'ejemplo-taxonomia';

    // Postype a los cuales está relacionada la taxonomia.
    protected static $postypes = [
        
    ];

    protected static $initialTerms = [
        'cine' => 'Cine'
    ];
}
