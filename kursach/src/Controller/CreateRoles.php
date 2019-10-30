<?php


namespace App\Controller;


use App\Entity\Role;
use App\Service\ConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateRoles extends AbstractController
{
    /**
     * @return Response
     * @Route("/hash")
     */
    public function createAction(ConfirmationService $confirm): Response
    {
        $hash = $confirm->builtSha256($this->getUser()->getEmail());
        return new Response("{$hash}");
    }
}