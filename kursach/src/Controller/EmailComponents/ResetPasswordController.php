<?php


namespace App\Controller\EmailComponents;


use App\Entity\User;
use App\Service\ConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Sodium\compare;

class ResetPasswordController extends AbstractController
{
    /**
     * @return Response
     * @Route("/reset", name="reset")
     */
    public function getCodeAction(Request $request, ConfirmationService $confirmationService, MailerInterface $mailer, UserPasswordEncoderInterface $encoder): Response
    {

        $form = $this->createFormBuilder()
            ->setMethod("GET")
            ->add('email', EmailType::class)
            ->add('get_code', SubmitType::class, ['label' => 'Get reset code'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                   'email' =>  $request->query->get("form")["email"],
                ]);

            if (!$user){
                throw $this->createNotFoundException("Email address does not exist");
            }

            $hash = $confirmationService->builtSha256($user->getEmail());
            $user->setUniqueHash($hash);
            $confirmationService->sendMailToUser($user, $mailer, $hash, "Reset password", 'email/reset_mail.html.twig');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        $reset_password = $this->createFormBuilder()
            ->setMethod("GET")
            ->add('Reset_code', TextType::class, ['required' => true])
            ->add('new_password', TextType::class, ['required' => true])
            ->add('reset', SubmitType::class)
            ->getForm();

        $reset_password->handleRequest($request);
        if ($reset_password->isSubmitted() && $reset_password->isValid()){

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'uniqueHash' =>  $request->query->get("form")["Reset_code"],
                ]);

            if (!$user){
                throw $this->createNotFoundException("Wrong reset code");
            }

            $user->setPassword($encoder->encodePassword($user, $request->query->get("form")["new_password"]));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

                return $this->render('home/index.html.twig');
        }

        return $this->render('email/reset_password.html.twig', [
            'form' => $form->createView(),
            'reset_form' => $reset_password->createView(),
        ]);
    }
}