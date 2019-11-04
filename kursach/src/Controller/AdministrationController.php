<?php

namespace App\Controller;

use App\Service\DataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministrationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/administration", name="administration")
     */
    public function administration(DataService $dataService): Response
    {
        return $this->render('administration/index.html.twig', [
            'users' => $dataService->getUsers(),
        ]);
    }
}
