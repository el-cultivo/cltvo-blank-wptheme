<?php 

namespace Illuminate\Mail;

class Mail
{
    public static function to($users)
    {
        return (new MailableMailer( new static() ))->to($users);
    }

    public function send($mailable)
    {
        return $mailable->send();
    }
}