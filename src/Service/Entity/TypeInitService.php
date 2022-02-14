<?php

namespace App\Service\Entity;

use App\Entity\Type;
use Doctrine\ORM\EntityManager;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class TypeInitService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getType(string $type): Type
    {
        /** @var EntityManager */
        $em = $this->entityManager;

        /** @var TypeRepository $repo*/
        $repo = $em->getRepository('TypeRepository');

        return $repo->findOneBy(['type' => $type]);
    }
}
