<?php

namespace App\Service\Entity;

use App\Entity\Type;
use Doctrine\ORM\EntityManager;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Type service
 */
class TypeInitService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * TypeInitService constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get a type
     *
     * @param string $type
     *
     * @return Type
     */
    public function getType(string $type): Type
    {
        /** @var EntityManager $em*/
        $em = $this->entityManager;

        /** @var TypeRepository $repo*/
        $repo = $em->getRepository(TypeRepository::class);

        return $repo->findOneBy(['type' => $type]);
    }
}
