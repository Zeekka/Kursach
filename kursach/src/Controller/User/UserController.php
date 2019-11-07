<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\EditForm;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/profile/{id}/show", name="user_show", methods={"GET"})
     */
    public function showAction(int $id): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'id' => $id,
            ]);

        $app_user_subscribes = $this->getUser()->getMySubscribes()->toArray();
        $subscribes_id = [];
        foreach($app_user_subscribes as $subscriber)
        {
            $subscribes_id[] = $subscriber->getId();
        }

        return $this->render('user/show.html.twig',[
                'user' => $user,
                'isSubscribedToUser' => in_array($id, $subscribes_id),
            ]);
    }

    /**
     * @Route("/user/profile/{id}/edit", name="user_edit", methods={"GET", "POST"})
     */
    public function editAction(int $id, Request $request): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'id' => $id,
            ]);

        $this->denyAccessUnlessGranted('edit', $user);

        $form = $this->createForm(EditForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()
                ->getManager();

            $em->persist($user);
            $em->flush();
        }
        return $this->render('user/edit.html.twig',[
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
