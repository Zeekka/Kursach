<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\EditForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile as UpFile;

class UserController extends AbstractController
{
    /**
     * @Route("/user/profile/{id<\d+>}/show", name="user_show", methods={"GET"})
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
                'isSubscribedToUser' => ($id !== $this->getUser()->getId()) ? in_array($id, $subscribes_id): true,
                'user_subscribes' => $user->getMySubscribes()->toArray(),
            ]);
    }

    /**
     * @Route("/user/profile/{id<\d+>}/edit", name="user_edit", methods={"GET", "POST"})
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

            if ($form['image']->getData() != new File("D:/Itransition/OSPanel/domains/withgit/Kursach/kursach/public/users_images/default.jpg")){
                /** @var UpFile $image */
                $image = $form['image']->getData();
                $imageName = $this->generateUniqueName().'.'.$image->guessExtension();
                $image->move($this->getParameter('image_directory'), $imageName);
                $user->setImage($imageName);
            }
            else{
                $user->setImage(substr($form['image']->getData(),75));
            }

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

    /**
     * @return string
     */
    private function generateUniqueName()
    {
        return md5(uniqid());
    }
}
