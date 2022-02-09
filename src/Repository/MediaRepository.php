<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\Trick;
use App\Repository\TypeRepository;
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
     * @var TypeRepository
     */
    private TypeRepository $typeRepository;

    /**
     * @var string
     */
    private string $uploadsDir;

    public function __construct(ManagerRegistry $registry, TypeRepository $typeRepository, string $uploadsDir)
    {
        $this->typeRepository    = $typeRepository;
        $this->uploadsDir        = $uploadsDir;

        parent::__construct($registry, Media::class);
    }

    /**
     * Add new trick thumbnail and delete the old one.
     *
     * @param Trick  $trick
     * @param string $fileName
     *
     * @return void
     */
    public function replaceThumbnail(Trick $trick, string $fileName)
    {
        $this->deleteCurrentThumbnail($trick);
        $this->persistThumbnail($trick, $fileName);
    }

    /**
     * Delete the current trick thumbnail.
     *
     * @param Trick $trick
     *
     * @return void
     */
    public function deleteCurrentThumbnail(Trick $trick)
    {
        /** @var array $medias */
        $medias = $trick->getMedia()->getValues();

        /** @var  Media $media */
        foreach ($medias as $key => $media) {
            if ($media->getType()->getType() === 'thumbnail') {
                $fileToDelete = $this->uploadsDir . '/' . $media->getPath();

                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                }

                $this->_em->remove($media);
                $this->_em->flush();
            }
        }
    }

    /**
     * Persist a new trick thumbnail in database.
     *
     * @param Trick  $trick
     * @param string $fileName
     *
     * @return void
     */
    public function persistThumbnail(Trick $trick, string $fileName)
    {
        $media = new Media();

        $media->setType($this->typeRepository->findOneBy(['type' => 'thumbnail']));
        $media->setTrick($trick);
        $media->setPath($fileName);

        $this->_em->persist($media);
        $this->_em->flush();
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
