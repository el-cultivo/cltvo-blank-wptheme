<?php

class Cltvo_Page_SpecialPage extends Cltvo_Page
{

    function __construct(){
        parent::__construct(
            get_post( $GLOBALS['special_pages_ids'][
                'SpecialPage_slug' // slug de la pagina especial
            ])
        );
    }

    public function setMetas()
    {

    }

}
