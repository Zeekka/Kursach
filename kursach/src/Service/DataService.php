<?php


namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class DataService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function getUsers(): array
    {
        $em = $this->em;
        $users = $em->getRepository(User::class)
            ->findAll();

        return $users;
    }
}