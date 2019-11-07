<?php


namespace App\Controller\User;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscribeController extends AbstractController
{
    /**
     * @Route("/user/subscribeto/{id}", name="subscribing", methods={"GET", "POST"})
     */
    public function subscribeAction(Request $request, int $id): Response
    {

        $subscribe_to_user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $user = $this->getUser();
        $user->addMySubscribe($subscribe_to_user);

        $em = $this->getDoctrine()
            ->getManager();
        $em->persist($user);
        $em->flush();

        return new RedirectResponse($request->headers->get("referer"));
    }

    /**
     * @Route("/user/unfollow/{id}", name="unfollow", methods={"GET", "POST"})
     */
    public function unfollowAction(Request $request, int $id): Response
    {
        $subscribe_to_user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $user = $this->getUser();
        $user->removeMySubscribe($subscribe_to_user);

        $em = $this->getDoctrine()
            ->getManager();
        $em->persist($user);
        $em->flush();

        return new RedirectResponse($request->headers->get("referer"));
    }
}