<?php

namespace App\Controller;

use App\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreferencesController extends AbstractController
{
    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/my_posts/preferences", name="preferences", methods={"GET"})
     */
    public function preferencesAction(Request $request): Response
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class);

        return $this->render('preferences/show.html.twig', [
            'preferences' => 'PreferencesController',
        ]);
    }
}
