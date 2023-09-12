<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Code\ConfirmCodeCreater;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSender
{
    public function __construct(
        public MailerInterface $mailer,
        public ConfirmCodeCreater $confirmCodeCreater
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(User $user): void
    {
        $confirmCode= $this->confirmCodeCreater->codeCreate($user);

        $email = (new Email())
            ->from($_ENV['DEFAULT_MAIL'])
            ->to($user->email)
            ->subject('Confirm code to WebAnt')
            ->text('Your confirm-code is '.$confirmCode->code);
//            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
    }

}