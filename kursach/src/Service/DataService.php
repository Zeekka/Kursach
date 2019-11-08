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
        $regex = "/^ROLE_/";

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
        } else if ($data["search_field"] === ''){
            $query = $em->createQuery(
                '
            SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles
            '
            );
        } else if (preg_match($regex, $data["search_field"])){

            $role_id = $em->getRepository(Role::class)
                ->findOneBy([
                    'role' => $data["search_field"],
                ]);
            $query = $em->createQuery(
            '
            SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles
            WHERE user.role_id = :param
            '
            )->setParameter('param', $role_id);
        } else if ($data["search_field"] === "Active" || $data["search_field"] === "Inactive"){
            $query = $em->createQuery(
                '
            SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles
            WHERE user.isActive = :param      
            '
            )->setParameter('param', $data["search_field"] === "Active" ? true : ($data["search_field"] === "Inactive" ? false : NULL ));
        } else {
            $query = $em->createQuery(
                '
            SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles
            WHERE user.id = :param
            OR user.email = :param
            OR user.firstName = :param
            OR user.lastName = :param      
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