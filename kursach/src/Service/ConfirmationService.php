<?php


namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime;
use Doctrine\ORM\EntityManagerInterface;

class ConfirmationService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function sendMailToUser(User $user, MailerInterface $mailer, string $subject, string $template): void
    {
        $email = (new  Mime\TemplatedEmail())
            ->from('adminmailer@mail.ru')
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
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

    public function sendMailToModerators(User $blogger, MailerInterface $mailer)
    {
        $em = $this->em;
        $query = $em->createQuery(
            '
            SELECT user
            FROM App\Entity\User user
            WHERE user.role_id >= :ROLE
            '
        )->setParameter('ROLE', 3);

        $result = $query->getResult();
        if (!$result){
            // TODO: throw proper exeption
        }

        $email = (new  Mime\TemplatedEmail())
            ->from('adminmailer@mail.ru');

        foreach ($result as $user)
        {
            $email->addTo($user->getEmail());
        }

        $email
            ->subject("New blogger registed! ")
            ->htmlTemplate('email/new_blogger_registed.html.twig')
            ->context([
               'blogger' => $blogger,
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return 1;
        }

        return null;
    }
}