<?php


namespace App\Controller;


use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateRoles extends AbstractController
{
    /**
     * @return Response
     * @Route("/create")
     */
    public function createAction(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $role = new Role();
        $role->setRole("ROLE_ADMIN");
        $em->persist($role);
        $em->flush();

        return new Response('<h1>Success</h1>');
    }
}