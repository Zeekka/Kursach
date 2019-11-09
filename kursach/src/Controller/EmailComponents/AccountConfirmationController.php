<?php

namespace App\Controller\EmailComponents;

use App\Entity\User;
use App\Service\ConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountConfirmationController extends AbstractController
{
    private $confirmationService;

    public function __construct(ConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

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
        $user->setUniqueHash($this->confirmationService->builtSha256($user->getEmail()));

        $em = $this->getDoctrine()->getManager();
        $em->flush();

       return new RedirectResponse($this->generateUrl('home'));
    }
}