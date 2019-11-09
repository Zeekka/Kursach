<?php

namespace App\Controller;

use App\Service\DataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdministrationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/administration", name="administration", methods={"GET", "POST"})
     */
    public function administration(DataService $dataService, Request $request): Response
    {

        if (!$request->query->all() || count($request->query->all()) == 1){
            $request->query->add(["sort_type" => '', "search_field" => '']);
        }

        return $this->render('administration/index.html.twig', [
            'users' => $dataService->getUsers($request, $request->query->all()),
        ]);
    }
}
