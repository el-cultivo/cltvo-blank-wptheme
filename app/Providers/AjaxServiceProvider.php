<?php

namespace App\Providers;

class AjaxServiceProvider
{
    protected $files = [
        \App\Http\Ajax\ContactAjax::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach($this->files as $ajax){
            $ajax = new $ajax;
            $ajax->registerAjax();
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
