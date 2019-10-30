<?php


namespace App\Controller\EmailComponents;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @return Response
     * @Route("/reset", name="reset")
     */
    public function getCodeAction(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->setMethod("GET")
            ->add('email', EmailType::class)
            ->add('get_code', SubmitType::class, ['label' => 'Get reset code'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                   'email' =>  $request->query->get("form")["email"]
                ]);

            if (!$user){
                throw $this->createNotFoundException("Email address does not exist");
            }
            //TODO: spot response
            return new Response($user->getFirstName());
        }

        return $this->render('user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}