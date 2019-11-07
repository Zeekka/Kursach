<?php


namespace App\Service;

use App\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class DataService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function getUsers(Request $request, array $data): object
    {
        $em = $this->em;
        $container = $this->container;

        if (!$data){
            $query = $em->createQuery(
                '
            SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles      
            '
            );
        }
        else {
            $query = $em->createQuery(
                '
            SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles
            WHERE user.id = :param
            OR user.lastName = :param
            OR user.email = :param        
            '
            )->setParameter('param', $data["search_field"]);
        }
        $paginator = $container->get('knp_paginator');
        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $users;
    }

    public function persistUserToDataBase(User $user): bool
    {
        $em = $this->em;
        $em->persist($user);

        try{
            $em->flush();
        }catch (\Exception $e)
        {
            $e->getMessage();
            return 1;
        }
        return 0;
    }
}