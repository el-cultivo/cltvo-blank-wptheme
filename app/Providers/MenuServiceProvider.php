<?php 

namespace App\Providers;

class MenuServiceProvider
{
    protected $menus = [
        'header_menu' => 'Header Menu',
        'footer_menu' => 'Footer Menu',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        register_nav_menus($this->menus);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    } 
}