<?php

namespace App;

use Illuminate\CustomPostType;

class Space extends CustomPostType
{
    protected static $supports = ['title', 'editor', 'excerpt', 'thumbnail'];

    public function setMetas(){}
}
