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

        $query_base =
            'SELECT
                user, roles
            FROM
                App\Entity\User user
            INNER JOIN user.role_id roles
            ';

        if (!$data) {
            $sort_param = '';
        }
        else{
            switch ($data) {
                case $data["sort_type"] === "email":
                    $sort_param = 'ORDER BY user.email';
                    break;
                case $data["sort_type"] === "firstName":
                    $sort_param = 'ORDER BY user.firstName';
                    break;
                case $data["sort_type"] === "lastName":
                    $sort_param = 'ORDER BY user.lastName';
                    break;
            }
        }

        if (!$data){
            $query = $em->createQuery(
             $query_base. $sort_param
            );
        } else if ($data["search_field"] === ''){
            $query = $em->createQuery(
                $query_base .$sort_param
            );
        } else if (preg_match($regex, $data["search_field"])){

            $role_id = $em->getRepository(Role::class)
                ->findOneBy([
                    'role' => $data["search_field"],
                ]);

            $query = $em->createQuery(
                $query_base. '
            WHERE user.role_id = :param
            '.$sort_param
            )->setParameter('param', $role_id);
        } else if ($data["search_field"] === "Active" || $data["search_field"] === "Inactive"){
            $query = $em->createQuery(
                $query_base.'
            WHERE user.isActive = :param      
            '.$sort_param
            )->setParameter('param', $data["search_field"] === "Active" ? true : ($data["search_field"] === "Inactive" ? false : NULL ));
        } else {
            $query = $em->createQuery(
                $query_base.
                '
            WHERE user.id = :param
            OR user.email = :param
            OR user.firstName = :param
            OR user.lastName = :param      
            '.$sort_param
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