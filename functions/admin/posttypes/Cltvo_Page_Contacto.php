<?php

class Cltvo_Page_Contacto extends Cltvo_Page
{
    public $social_net;

    function __construct(){
        parent::__construct(
            get_post( $GLOBALS['special_pages_ids']['contacto'])
        );
    }


    public function setMetas()
    {
        $this->social_net = Cltvo_SocialNet::getMetaValue($this->post);
    }

}
