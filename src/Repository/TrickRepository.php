<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * Used to update a trick
     *
     * @param Trick $trick
     *
     * @return void
     */
    public function update(Trick $trick): void
    {
        $this->_em->persist($trick);
        $this->_em->flush();
    }

    /**
     * Remove a trick
     *
     * @param Trick $trick
     *
     * @return void
     */
    public function delete(Trick $trick): void
    {
        $this->_em->remove($trick);
        $this->_em->flush();
    }
}
