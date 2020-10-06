<?php

namespace App\Http\Ajax;

use Illuminate\Ajax;
use App\Contacto;
use App\Mail\ContactMail;
use App\Mail\AdminContactMail;
use Illuminate\Mail\Mail;

class ContactAjax extends Ajax
{
    private $from_address;

    private $error_messages = [
        'development' => 'Error de configuración del plugin de mailgun en el admin',
        'production' => 'Hubo un error al procesar su petición'
    ];

    public function store($input)
    {

        $this->validate($input, [
            'name'          => 'required',
            'email'         => 'required',
        ]);

        $this->from_address = $this->getMailgunFromAddress();

        // Mail para el comprador.
        Mail::to($input['email'])->send(new ContactMail($input));

        // Mail para el admin
        Mail::to($this->from_address)->send(new AdminContactMail($input));

        $this->success( __('Gracias por contactarnos, pronto tendrás noticias de nosotros.') );
    }

    /*
        Función que obtendrá los settings de mailgun si está activo y devolverá un mensaje de error si no está configurado el plugin
    */
    private function getMailgunFromAddress(){

        $from_address = null;

        $error_message = (WP_DEBUG)
            ? $error_messages['development']
            : $error_messages['production'] ;

        //Si el plugin está activo
        if ( is_plugin_active( 'mailgun/mailgun.php' ) ){

            //Obtenemos las opciones del mismo
            $mg_opts = get_option('mailgun');

            $from_address = !empty( $mg_opts['from-address'] ) ? $mg_opts['from-address'] : null ;
        }

        //Si el resultado está vacío, retornamos el error
        if( empty($from_address) ) {

            $this->returnError($error_message);

        }

        return $from_address;

    }
}
