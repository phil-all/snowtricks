<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Trick;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Repository\StatusRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * @var StatusRepository
     */
    private StatusRepository $statusRepository;

    public function __construct(ManagerRegistry $registry, StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;

        parent::__construct($registry, Comment::class);
    }

    /**
     * Initialiaze a comment
     *
     * @param User  $user
     * @param Trick $trick
     *
     * @return Comment
     */
    public function init(User $user, Trick $trick): Comment
    {
        $comment = new Comment();

        $status = $this->statusRepository->findOneBy(['status' => 'active']);

        return $comment
            ->setUser($user)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setStatus($status)
            ->setTrick($trick);
    }

    /**
     * Persist a comment in database
     *
     * @param Comment $comment
     *
     * @return void
     */
    public function persist(Comment $comment): void
    {
        $this->_em->persist($comment);
        $this->_em->flush();
    }
}
