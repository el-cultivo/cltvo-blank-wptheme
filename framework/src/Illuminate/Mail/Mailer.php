<?php

namespace Illuminate\Mail;

class Mailer
{
    public function send(Mailable $mailable)
    {
        $headers = [
            'FROM: ' . $mailable->from['name']  . '<' . $mailable->from['address'] . '>'
        ];

        if(!empty($cc)){
            $headers[] = 'CC:' . implode(",", $cc);
        }

        if(!empty($bcc)){
            $headers[] = 'BCC:' . implode(",", $bcc);
        }

        $headers = array_merge($headers, [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ]);

        if (function_exists('wp_mail')) {
            return wp_mail(
                $mailable->to['address'],
                $mailable->subject,
                $mailable->parseView(),
                implode("\r\n", $headers)
            );
        } else {
            return mail(
                $mailable->to['address'],
                $mailable->subject,
                $mailable->parseView(),
                implode("\r\n", $headers)
            );
        }

    }
}
