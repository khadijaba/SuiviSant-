<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $message)
    {
        $email = (new Email())
            ->from('benayedkhadija5@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);
    }

    
}