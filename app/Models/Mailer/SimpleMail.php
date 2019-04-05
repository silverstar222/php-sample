<?php

namespace App\Models\Mailer;

class SimpleMail implements MailerInterface
{
    private $to;
    private $from;
    private $subject;
    private $text;

    /**
     * SimpleMail constructor.
     *
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $text
     */
    public function __construct($to, $from, $subject, $text) {
        $this->to = $to;
        $this->from = $from;
        $this->subject = $subject;
        $this->text = $text;
    }

    public function send():void
    {
        $headers = "From: " . $this->from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html;boundary='boundary'; charset=UTF-8\r\n";
        mail($this->to, $this->subject, $this->text, $headers);
    }
}