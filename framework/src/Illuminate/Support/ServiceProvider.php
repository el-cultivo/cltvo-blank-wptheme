<?php

namespace Illuminate\Support;

use Illuminate\Foundation\Application;

class ServiceProvider 
{
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}