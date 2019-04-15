<?php

namespace App\Providers;

class TaxonomyServiceProvider
{
    protected $taxonomies = [
        // Filtros - se pueden dividir por espacios o temas
        //\App\Taxonomies\ExampleTaxonomie::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach($this->taxonomies as $taxonomy){
            $taxonomy::registerTaxonomy();
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
