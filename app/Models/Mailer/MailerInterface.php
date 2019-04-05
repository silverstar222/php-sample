<?php

namespace App\Models\Mailer;

interface MailerInterface
{
    /**
     * @return void
     */
    public function send();
}