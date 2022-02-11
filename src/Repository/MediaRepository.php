<?php

namespace App\Repository;

use App\Entity\Type;
use App\Entity\Media;
use App\Entity\Trick;
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
     * MediaRepository constructor
     *
     * @param ManagerRegistry $registry
     * @param string          $uploadsDir
     */
    public function __construct(ManagerRegistry $registry, string $uploadsDir)
    {
        $this->uploadsDir        = $uploadsDir;

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
            ->changeMediaPath($media, $path);
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
        /** @var string $fileToDelete */
        $fileToDelete = $this->uploadsDir . '/' . $media->getPath();

        $this->deleteFile($fileToDelete);

        return $this;
    }

    /**
     * Change a media path
     *
     * @param Media  $media
     * @param string $path
     *
     * @return void
     */
    public function changeMediaPath(Media $media, string $path): void
    {
        $media->setPath($path);
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
     * Delete a file
     *
     * @param string $completeFilePath
     *
     * @return void
     */
    private function deleteFile(string $completeFilePath): void
    {
        if (file_exists($completeFilePath) && !is_dir($completeFilePath)) {
            unlink($completeFilePath);
        }
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
