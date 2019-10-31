<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegistrationForm;
use App\Service\ConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request, UserPasswordEncoderInterface $encoder, ConfirmationService $confirmationService, MailerInterface $mailer)
    {
        $user = new User($encoder);
        $form = $this->createForm(RegistrationForm::class, $user);

        $em = $this->getDoctrine()->getManager();

        $emrole = $this->getDoctrine()
            ->getRepository(Role::class)
            ->findOneBy([
                'role' => "ROLE_USER"
            ]);

        if (!$emrole){
            throw new \Exception("ROLE_USER not found");
        }

        $emrole->addUser($user);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $hash = $confirmationService->builtSha256($form->getData()->getEmail());
            $user->setUniqueHash($hash);

            $confirmationService->sendMailToUser($user, $mailer, $hash, "Confirmation", 'email/register.html.twig');

            $em->persist($user);
            $em->flush();

            return $this->render('email/check_your_email.html.twig');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
