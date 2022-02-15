<?php

namespace App\Repository;

use App\Entity\Type;
use App\Entity\Media;
use App\Entity\Trick;
use App\Service\Eraser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private string $uploadsDir;

    /**
     * @var Eraser
     */
    private Eraser $eraser;

    /**
     * MediaRepository constructor
     *
     * @param ManagerRegistry $registry
     * @param Eraser          $eraser
     * @param string          $uploadsDir
     */
    public function __construct(ManagerRegistry $registry, Eraser $eraser, string $uploadsDir)
    {
        $this->eraser     = $eraser;
        $this->uploadsDir = $uploadsDir;

        parent::__construct($registry, Media::class);
    }

    /**
     * Replace a trick media
     *
     * @param Media       $media
     * @param string|null $path
     *
     * @return void
     */
    public function replaceTrickMedia(Media $media, ?string $path): void
    {
        $this
            ->deleteTrickMediaFile($media)
            ->setMediaPath($media, $path);
    }

    /**
     * Delete a media and its file
     *
     * @param Media $media
     *
     * @return void
     */
    public function deleteMedia(Media $media): void
    {
        $this->deleteTrickMediaFile($media);

        $this->_em->remove($media);
        $this->_em->flush();
    }

    /**
     * Delete a trick media file
     *
     * @param Media $media
     *
     * @return self
     */
    public function deleteTrickMediaFile(Media $media): self
    {
        if (null !== $media->getId()) {
            $this->eraser->deleteFile($this->getCompleteFilePath($media));
        }

        return $this;
    }

    /**
     * Create a trick media
     *
     * @param Type   $type
     * @param Trick  $trick
     * @param string $path
     *
     * @return void
     */
    public function createTrickMedia(Type $type, Trick $trick, string $path): void
    {
        $media = new Media();

        $media->setType($type)->setTrick($trick)->setPath($path);

        $this->_em->persist($media);
        $this->_em->flush();
    }

    /**
     * set a media path
     *
     * @param Media  $media
     * @param string $path
     *
     * @return void
     */
    private function setMediaPath(Media $media, string $path): void
    {
        $media->setPath($path);
    }

    /**
     * Get the complete path of a Media file
     *
     * @param Media $media
     *
     * @return string
     */
    private function getCompleteFilePath(Media $media): string
    {
        return $this->uploadsDir . '/' . $media->getPath();
    }

    // /**
    //  * @return Media[] Returns an array of Media objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Media
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
