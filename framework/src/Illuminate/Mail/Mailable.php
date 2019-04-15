<?php 

namespace Illuminate\Mail;

class Mailable
{
    public $to = [
        'address' => '',
        'name' => '',
    ];

    public $cc = [];
    
    public $bcc = [];

    public $subject;

    public $from = [
        'address' => '',
        'name' => '',
    ];

    public $view = 'mail/layout.php';

    public $attachments = [];

    public function to($address, $name = '')
    {
        $this->to['address'] = $address;
        $this->to['name'] = $name;

        return $this;
    }

    public function cc($users)
    {
        $this->cc = $users;

        return $this;
    }

    public function bcc($users)
    {
        $this->bcc = $users;

        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function from($address, $name = '')
    {
        $this->from['address'] = $address;
        $this->from['name'] = $name;

        return $this;
    }

    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    public function attach($attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    public function parseView()
    {
        ob_start();

        extract( get_object_vars($this) );

        include get_template_directory() . '/' . $this->view;

        $content = ob_get_clean();

        return $content;
    }

    public function send()
    {
        $this->build();

        if(WP_DEBUG){
            return (new SMTPMailer)->send($this);
        }else {
            return (new Mailer)->send($this);
        }
    }
}