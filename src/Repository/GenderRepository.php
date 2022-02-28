<?php

namespace App\Repository;

use App\Entity\Gender;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Gender|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gender|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gender[]    findAll()
 * @method Gender[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gender::class);
    }
}
