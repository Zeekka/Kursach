<?php


namespace App\Form;

use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Security;

class EditForm extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options )
    {
        $user = $this->security->getUser();

        $builder
            ->add('email', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => "Active",
                ])
            ->add('role_id', EntityType::class, [
                'class' => Role::class,
                'label' => "Role",
                'choice_label' => 'role',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('role')
                        ->where('role.id < :ROLE')
                        ->setParameter('ROLE', $user->getRoleId()->getId());
           }])
            ->add('submit', SubmitType::class, ['label' => 'Save Changes'])
            ->getForm();

    }
}