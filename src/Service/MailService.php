<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(
        protected MailerInterface $mailer
    )
    {
    }

    public function sendCongratulationsEmail(
        string $userEmail,
        string $unicornName,
        int    $postCount
    ): void
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($userEmail)
            ->subject(
                sprintf(
                    'Congratulations on your purchase of your unicorn, %s!',
                    $unicornName
                )
            )
            ->text(sprintf('%d posts were deleted!', $postCount));

        $this->mailer->send($email);
    }
}