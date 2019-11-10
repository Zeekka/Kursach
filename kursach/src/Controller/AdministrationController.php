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
     * @Route("/administration/users", name="administration_users", methods={"GET", "POST"})
     */
    public function administrationUsers(DataService $dataService, Request $request): Response
    {

        if (!$request->query->all() || count($request->query->all()) == 1){
            $request->query->add(["sort_type" => '', "search_field" => '']);
        }

        return $this->render('administration/index.html.twig', [
            'users' => $dataService->getUsers($request, $request->query->all()),
        ]);
    }
    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/administration/posts", name="administration_posts", methods={"GET", "POST"})
     */
    public function administrationPosts(DataService $dataService, Request $request): Response
    {

        if (!$request->query->all() || count($request->query->all()) == 1){
            $request->query->add(["sort_type" => '', "search_field" => '']);
        }

        return $this->render('administration/posts.html.twig', [
            'users' => $dataService->getUsers($request, $request->query->all()),
        ]);
    }
}