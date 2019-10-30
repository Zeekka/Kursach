<?php

namespace App\Controller\EmailComponents;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountConfirmationController extends AbstractController
{
    /**
     * @return Response
     * @Route("/confirmation/user/{hash}", name="email_confirmation")
     */
    public function confirmAction(string $hash): Response
    {
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'uniqueHash' => $hash,
            ]);

        if (!$user){
            throw $this->createNotFoundException("");
        }

        $user->setIsActive(true);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

       return new RedirectResponse($this->generateUrl('home'));
    }
}