<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AdminContactMail extends Mailable
{
    public $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function build()
    {
        return $this->subject('Información de contacto')
                    ->from($this->to['address'])
                    ->view('mail/contact.php');
    }
}
