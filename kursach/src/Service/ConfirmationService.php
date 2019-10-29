<?php


namespace App\Service;


use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime;

class ConfirmationService
{
    public function sendMailToUser(User $user, MailerInterface $mailer): void
    {
        $email = (new  Mime\TemplatedEmail())
            ->from('adminmailer@mail.ru')
            ->to($user->getEmail())
            ->subject('Confirmation account')
            ->htmlTemplate('email/register.html.twig');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {

        }
    }
}