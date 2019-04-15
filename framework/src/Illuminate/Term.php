<?php 

namespace Illuminate;

class Term
{
    public static function find($id, $taxonomy = 'category')
    {
        return get_term_by('id', $id, $taxonomy);
    }
}