<?php 

namespace Illuminate\Mail;

use PHPMailer\PHPMailer\PHPMailer;

class SMTPMailer 
{
    protected $mailable;

    protected $mail_driver = 'smtp';
    
    protected $mail_host = 'smtp.mailtrap.io';
    
    protected $mail_port = 25;
    
    protected $mail_encryption = 'tls';
    
    protected $mail_username = 'f9c358c708042e';
    
    protected $mail_password = '68d9fb9afcbd49';
    
    protected $mail_from = [
        'address' => 's@elcultivo.mx',
        'name' => 'Sergio Fonseca'
    ];
    
    protected $mail_debug = false;

    public function send(Mailable $mailable)
    {
        $mailer = new PHPMailer;

        // Configuration.

        $mailer->SMTPDebug = 0;

        $mailer->isSMTP();

        $mailer->Host = $this->mail_host;
        $mailer->SMTPAuth = true;
        $mailer->Username = $this->mail_username;
        $mailer->Password = $this->mail_password;
        $mailer->SMTPSecure = $this->mail_encryption;
        $mailer->Port = $this->mail_port;

        $mailer->isHTML(true);

        // Set the from.
        $mailer->setFrom($mailable->from['address'], $mailable->from['name']);

        // Set the recipient.
        $mailer->addAddress($mailable->to['address'], $mailable->to['name']);

        // Set CC
        foreach($mailable->cc as $cc){
            $mailer->addCC($cc);
        }

        // Set BCC
        foreach($mailable->bcc as $bcc){
            $mailer->addBCC($bcc);
        }

        // Set the attachments
        foreach($mailable->attachments as $attachment){
            $mailer->addAttachment($attachment);
        }

        // Set the subject.
        $mailer->Subject = utf8_decode($mailable->subject);

        // Set the body.
        $mailer->Body = utf8_decode($mailable->parseView());
        
        $mailer->send();
    }
}