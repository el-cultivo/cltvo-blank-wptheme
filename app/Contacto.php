<?php

namespace App;

use Illuminate\Page;
use App\Metaboxes\CltvoSocialNet;

class Contacto extends Page
{
    public $social_net;

    public $mailables = [
        'mail'
    ];

    function __construct()
    {
        parent::__construct( specialPage('contacto', true) );
    }

    public function setMetas()
    {
        $this->social_net = $this->getSocialNets();

        foreach($this->mailables as $mailable){

            if(!array_key_exists($mailable, $this->social_net) || !$this->social_net[$mailable]){
                $this->social_net[$mailable] = 'info@zonapaz.com';
            }

        }
    }

    protected function getSocialNets()
    {
        return CltvoSocialNet::getMetaValue($this->post);
    }
}
