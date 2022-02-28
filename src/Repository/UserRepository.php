<?php

namespace App\Repository;

use App\Entity\User;
use DateTimeImmutable;
use App\Repository\StatusRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var StatusRepository
     */
    private StatusRepository $status;

    public function __construct(ManagerRegistry $registry, StatusRepository $status)
    {
        $this->status = $status;

        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param PasswordAuthenticatedUserInterface $user
     * @param string                             $newHashedPassword
     *
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Used to create a new pending user.
     *
     * @param User                        $user
     * @param UserPasswordHasherInterface $hasher
     * @param string                      $password
     *
     * @return void
     */
    public function createPendingUser(User $user, UserPasswordHasherInterface $hasher, string $password): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user
            ->setStatus($this->status->findOneBy(['status' => 'pending']))
            ->setRoles(['ROLE_USER'])
            ->setPassword($hasher->hashPassword($user, $password));

        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Used to activate a pending user.
     *
     * @param User $user
     *
     * @return void
     */
    public function userActivation(User $user): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setStatus($this->status->findOneBy(['status' => 'active']))
            ->setRegistredAt(new DateTimeImmutable('now'));

        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Change password
     *
     * @param User                        $user
     * @param UserPasswordHasherInterface $hasher
     * @param string                      $password
     *
     * @return void
     */
    public function changePassword(User $user, UserPasswordHasherInterface $hasher, string $password): void
    {
        $user->setPassword($hasher->hashPassword($user, $password));

        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Find an user by its email
     *
     * @param string $value
     * @return User|null
     */
    public function findOneByEmail(string $value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
