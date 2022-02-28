<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Return category list with given category on top.
     * Used in update trick, to display by default trick category in choice list.
     *
     * @param integer $value
     *
     * @return array
     */
    public function onTopOfList(int $value): array
    {

        $entityManager = $this->getEntityManager();

        $onTop = $entityManager->createQuery(
            'SELECT c FROM App\Entity\category c WHERE c.id = :value'
        )->setParameter('value', $value);

        $residuals = $entityManager->createQuery(
            'SELECT c FROM App\Entity\category c WHERE c.id != :value'
        )->setParameter('value', $value);

        return array_merge($onTop->getResult(), $residuals->getResult());
    }
}
