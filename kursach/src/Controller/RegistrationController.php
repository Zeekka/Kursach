<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User($encoder);
        $form = $this->createForm(RegistrationForm::class, $user);

        $em = $this->getDoctrine()->getManager();
        // TODO: throw exeption if not find ROLE_USER
        $emrole = $this->getDoctrine()
            ->getRepository(Role::class)
            ->findOneBy([
                'role' => "ROLE_USER"
            ]);

        $form->getData()->setRoleId($emrole);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em->persist($user);
            $em->flush();

            return new RedirectResponse($this->generateUrl('home'));
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
