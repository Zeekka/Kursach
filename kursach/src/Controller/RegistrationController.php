<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegistrationForm;
use App\Service\ConfirmationService;
use App\Service\DataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile as UpFile;

class RegistrationController extends AbstractController
{
    private $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request, UserPasswordEncoderInterface $encoder, ConfirmationService $confirmationService, MailerInterface $mailer)
    {
        // TODO: dinamicly encode password
        $user = new User();
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

            $user->setPassword($encoder->encodePassword($user, $form->getData()->getPassword()));
            $hash = $confirmationService->builtSha256($form->getData()->getEmail());
            $user->setUniqueHash($hash);

            /** @var UpFile $image */
            $image = $form['image']->getData();
            $imageName = $this->generateUniqueName().'.'.$image->guessExtension();
            copy($image->getPathname(), $this->getParameter('image_directory')."/".$imageName);
            $user->setImage($imageName);

            $confirmationService->sendMailToUser($user, $mailer, "Confirmation", 'email/register.html.twig');

            $this->dataService->persistUserToDataBase($user);

            if ($user->getIsBloger()){
                $confirmationService->sendMailToModerators($user, $mailer);
            }

            return $this->render('email/check_your_email.html.twig');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     /**
     * @return string
     */
    private function generateUniqueName()
    {
        return md5(uniqid());
    }
}
