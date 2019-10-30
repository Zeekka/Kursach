<?php


namespace App\Service;


use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime;

class ConfirmationService
{
    public function sendMailToUser(User $user, MailerInterface $mailer, string $hash): void
    {
        // TODO: attach hash to request
        $email = (new  Mime\TemplatedEmail())
            ->from('adminmailer@mail.ru')
            ->to($user->getEmail())
            ->subject('Confirmation account')
            ->htmlTemplate('email/register.html.twig')
            ->context([
                'user' => $user,
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {

        }
    }

    public function builtSha256(string $email): string
    {
        $date = new \DateTimeImmutable();
        return hash_hmac("sha256", $email, $date->format("Y-m-d H:i:s P"));
    }
}