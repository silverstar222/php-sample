<?php

namespace App\Models\Mailer;

class Mailer
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * Mailer constructor.
     *
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    public function sendEmail()
    {
        $this->mailer->send();
    }
}