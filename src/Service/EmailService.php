<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRendezvousConfirmation(string $recipientEmail, string $recipientName, string $date, string $time, string $location): void
    {
        $email = (new Email())
            ->from('mahmoudmsolii@gmail.com')
            ->to($recipientEmail)
            ->subject('Rendezvous Confirmation')
            ->text("
                Hello $recipientName,
                
                Your rendezvous has been scheduled:
                Date: $date
                Time: $time
                Location: $location
                
                Thank you!
            ");

        $this->mailer->send($email);
    }
}
